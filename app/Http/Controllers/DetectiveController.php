<?php

namespace App\Http\Controllers;

use App\Events\DetectiveRoomUpdated;
use App\Models\DetectiveCase;
use App\Models\DetectivePlayer;
use App\Models\DetectiveQuestion;
use App\Models\DetectiveRoom;
use App\Models\DetectiveTeam;
use App\Services\DetectiveCaseLibraryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DetectiveController extends Controller
{
    /**
     * Entry Hub for The Problem Detective.
     */
    public function hub(): View
    {
        DetectiveCaseLibraryService::ensureDefaultCasesExist();

        return view('games.detective.hub');
    }

    /**
     * Game Master (Host) Dashboard.
     */
    public function host(Request $request): View
    {
        DetectiveCaseLibraryService::ensureDefaultCasesExist();

        $code = strtoupper((string) $request->query('code', ''));
        $room = null;

        if ($code !== '') {
            $room = DetectiveRoom::with(['teams.players', 'detectiveCase'])->where('code', $code)->first();
        }

        if (! $room) {
            $room = $this->createDefaultRoom();
        }

        $cases = DetectiveCase::orderBy('is_default', 'desc')->orderBy('id')->get();

        return view('games.detective.host', [
            'room' => $room,
            'cases' => $cases,
        ]);
    }

    /**
     * Player (Detective) screen.
     */
    public function play(Request $request): View
    {
        DetectiveCaseLibraryService::ensureDefaultCasesExist();

        return view('games.detective.play', [
            'initialCode' => strtoupper((string) $request->query('code', '')),
        ]);
    }

    /**
     * Classroom Projector Mode (16:9 display, zero secret leaks).
     */
    public function projector(Request $request): View
    {
        DetectiveCaseLibraryService::ensureDefaultCasesExist();

        return view('games.detective.projector', [
            'initialCode' => strtoupper((string) $request->query('code', '')),
        ]);
    }

    /**
     * Create a brand new multiplayer detective room.
     */
    public function createRoom(): JsonResponse
    {
        DetectiveCaseLibraryService::ensureDefaultCasesExist();
        $room = $this->createDefaultRoom();

        return response()->json([
            'success' => true,
            'code' => $room->code,
            'host_token' => $room->host_token,
            'host_url' => route('games.problem-detective.host', ['code' => $room->code]),
            'join_url' => route('games.problem-detective.play', ['code' => $room->code]),
            'projector_url' => route('games.problem-detective.projector', ['code' => $room->code]),
        ]);
    }

    /**
     * Fetch authoritative room state tailored strictly by role & token.
     */
    public function state(Request $request): JsonResponse
    {
        $code = strtoupper((string) $request->query('code', ''));
        $room = DetectiveRoom::with(['detectiveCase', 'teams.players', 'questions.player', 'questions.team'])
            ->where('code', $code)
            ->first();

        if (! $room) {
            return response()->json([
                'success' => false,
                'message' => 'Room tidak ditemukan.',
            ], 404);
        }

        $role = (string) $request->query('role', 'projector');
        $hostToken = (string) ($request->header('X-Host-Token') ?? $request->query('host_token', ''));
        $playerToken = (string) ($request->header('X-Player-Token') ?? $request->query('player_token', ''));

        $isAuthorizedHost = $role === 'host' && $hostToken !== '' && hash_equals($room->host_token, $hostToken);

        $player = null;
        if ($playerToken !== '') {
            $player = DetectivePlayer::where('detective_room_id', $room->id)
                ->where('player_token', $playerToken)
                ->first();

            if ($player) {
                $player->update(['last_seen_at' => now()]);
            }
        }

        $remainingSeconds = $this->computeRemainingSeconds($room);

        if ($isAuthorizedHost) {
            return response()->json([
                'success' => true,
                'role' => 'host',
                'server_time' => now()->toISOString(),
                'remaining_seconds' => $remainingSeconds,
                'room' => $this->formatRoomForHost($room),
            ]);
        }

        if ($role === 'player') {
            return response()->json([
                'success' => true,
                'role' => 'player',
                'server_time' => now()->toISOString(),
                'remaining_seconds' => $remainingSeconds,
                'room' => $this->formatRoomForPlayer($room, $player),
            ]);
        }

        return response()->json([
            'success' => true,
            'role' => 'projector',
            'server_time' => now()->toISOString(),
            'remaining_seconds' => $remainingSeconds,
            'room' => $this->formatRoomForProjector($room),
        ]);
    }

    /**
     * Player joins a room and picks a team, or reconnects using existing token.
     */
    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'name' => ['required', 'string', 'min:2', 'max:40'],
            'detective_team_id' => ['nullable', 'integer'],
            'player_token' => ['nullable', 'string'],
        ]);

        $code = strtoupper($validated['code']);
        $room = DetectiveRoom::with('teams')->where('code', $code)->first();

        if (! $room) {
            return response()->json(['success' => false, 'message' => 'Kode room tidak ditemukan!'], 404);
        }

        // Reconnect check via existing player_token
        if (! empty($validated['player_token'])) {
            $existing = DetectivePlayer::where('detective_room_id', $room->id)
                ->where('player_token', $validated['player_token'])
                ->first();
            if ($existing) {
                $existing->update(['last_seen_at' => now()]);

                return response()->json([
                    'success' => true,
                    'reconnected' => true,
                    'player_token' => $existing->player_token,
                    'player_id' => $existing->id,
                    'team_id' => $existing->detective_team_id,
                    'name' => $existing->name,
                ]);
            }
        }

        $trimmedName = trim($validated['name']);
        $duplicateName = DetectivePlayer::where('detective_room_id', $room->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($trimmedName)])
            ->exists();

        if ($duplicateName) {
            return response()->json([
                'success' => false,
                'message' => 'Nama "'.$trimmedName.'" sudah digunakan di room ini.',
            ], 422);
        }

        $teamId = $validated['detective_team_id'] ?? null;
        if (! $teamId) {
            // Auto-assign to team with lowest member count below capacity
            $team = $room->teams->sortBy(fn (DetectiveTeam $t) => $t->players()->count())->first();
            $teamId = $team?->id;
        }

        $team = DetectiveTeam::where('detective_room_id', $room->id)->where('id', $teamId)->first();
        if (! $team) {
            return response()->json(['success' => false, 'message' => 'Kelompok tidak ditemukan.'], 422);
        }

        $currentCount = $team->players()->count();
        if ($currentCount >= $team->max_capacity) {
            return response()->json([
                'success' => false,
                'message' => $team->name.' sudah penuh ('.$currentCount.'/'.$team->max_capacity.'). Pilih kelompok lain!',
            ], 422);
        }

        $player = DetectivePlayer::create([
            'detective_room_id' => $room->id,
            'detective_team_id' => $team->id,
            'name' => $trimmedName,
            'player_token' => Str::random(40),
            'is_ready' => false,
            'last_seen_at' => now(),
        ]);

        $this->broadcastUpdate($room, 'player_joined', ['player' => $player->name]);

        return response()->json([
            'success' => true,
            'player_token' => $player->player_token,
            'player_id' => $player->id,
            'team_id' => $player->detective_team_id,
            'name' => $player->name,
        ]);
    }

    /**
     * Allow player to change team in lobby.
     */
    public function switchTeam(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'player_token' => ['required', 'string'],
            'detective_team_id' => ['required', 'integer'],
        ]);

        $room = DetectiveRoom::where('code', strtoupper($validated['code']))->firstOrFail();
        if ($room->phase !== 'lobby') {
            return response()->json(['success' => false, 'message' => 'Pindah kelompok hanya diizinkan saat di Lobby.'], 422);
        }

        $player = $this->authenticatePlayer($room, $validated['player_token']);
        $team = DetectiveTeam::where('detective_room_id', $room->id)->where('id', $validated['detective_team_id'])->firstOrFail();

        if ($team->id !== $player->detective_team_id && $team->players()->count() >= $team->max_capacity) {
            return response()->json(['success' => false, 'message' => $team->name.' sudah penuh!'], 422);
        }

        $player->update(['detective_team_id' => $team->id]);
        $this->broadcastUpdate($room, 'team_switched');

        return response()->json(['success' => true, 'team_id' => $team->id]);
    }

    /**
     * Player toggles ready status in lobby.
     */
    public function toggleReady(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'player_token' => ['required', 'string'],
        ]);

        $room = DetectiveRoom::where('code', strtoupper($validated['code']))->firstOrFail();
        $player = $this->authenticatePlayer($room, $validated['player_token']);

        $player->update(['is_ready' => ! $player->is_ready]);
        $this->broadcastUpdate($room, 'player_ready');

        return response()->json(['success' => true, 'is_ready' => $player->is_ready]);
    }

    /**
     * Host updates room configuration (case, team count/names/capacities, lives, durations, investigation mode).
     */
    public function updateConfig(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);

        $validated = $request->validate([
            'detective_case_id' => ['nullable', 'integer', 'exists:detective_cases,id'],
            'investigation_mode' => ['nullable', 'in:private,public'],
            'target_players' => ['nullable', 'integer', 'min:1', 'max:100'],
            'default_lives' => ['nullable', 'integer', 'min:1', 'max:50'],
            'phase_durations' => ['nullable', 'array'],
            'teams' => ['nullable', 'array', 'min:1', 'max:12'],
            'teams.*.id' => ['nullable', 'integer'],
            'teams.*.name' => ['required_with:teams', 'string', 'min:1', 'max:60'],
            'teams.*.max_capacity' => ['required_with:teams', 'integer', 'min:1', 'max:30'],
        ]);

        DB::transaction(function () use ($room, $validated): void {
            if (isset($validated['detective_case_id'])) {
                $room->detective_case_id = $validated['detective_case_id'];
            }
            if (isset($validated['investigation_mode'])) {
                $room->investigation_mode = $validated['investigation_mode'];
            }
            if (isset($validated['target_players'])) {
                $room->target_players = $validated['target_players'];
            }
            if (isset($validated['default_lives'])) {
                $newLives = $validated['default_lives'];
                $room->default_lives = $newLives;
                if ($room->phase === 'lobby') {
                    DetectiveTeam::where('detective_room_id', $room->id)->update(['lives' => $newLives]);
                }
            }
            if (isset($validated['phase_durations'])) {
                $current = $room->phase_durations ?? DetectiveRoom::DEFAULT_PHASE_DURATIONS;
                foreach (['briefing', 'discussion', 'investigation', 'deduction'] as $p) {
                    if (isset($validated['phase_durations'][$p])) {
                        $current[$p] = max(30, min(3600, (int) $validated['phase_durations'][$p]));
                    }
                }
                $room->phase_durations = $current;
            }
            $room->save();

            if (isset($validated['teams'])) {
                $keptIds = [];
                foreach ($validated['teams'] as $idx => $teamData) {
                    $team = null;
                    if (! empty($teamData['id'])) {
                        $team = DetectiveTeam::where('detective_room_id', $room->id)->where('id', $teamData['id'])->first();
                    }
                    if ($team) {
                        $team->update([
                            'name' => trim($teamData['name']),
                            'max_capacity' => $teamData['max_capacity'],
                            'sort_order' => $idx + 1,
                        ]);
                    } else {
                        $team = DetectiveTeam::create([
                            'detective_room_id' => $room->id,
                            'name' => trim($teamData['name']),
                            'max_capacity' => $teamData['max_capacity'],
                            'sort_order' => $idx + 1,
                            'lives' => $room->default_lives,
                        ]);
                    }
                    $keptIds[] = $team->id;
                }
                // Reassign players if a team was removed
                $fallbackTeamId = $keptIds[0] ?? null;
                $removedTeamIds = DetectiveTeam::where('detective_room_id', $room->id)
                    ->whereNotIn('id', $keptIds)
                    ->pluck('id');
                if ($removedTeamIds->isNotEmpty() && $fallbackTeamId) {
                    DetectivePlayer::whereIn('detective_team_id', $removedTeamIds)
                        ->update(['detective_team_id' => $fallbackTeamId]);
                    DetectiveTeam::whereIn('id', $removedTeamIds)->delete();
                }
            }
        });

        $this->broadcastUpdate($room, 'config_updated');

        return response()->json(['success' => true, 'message' => 'Konfigurasi room diperbarui.']);
    }

    /**
     * Host advances or switches game phase.
     */
    public function setPhase(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);

        $validated = $request->validate([
            'phase' => ['required', 'in:'.implode(',', DetectiveRoom::PHASES)],
        ]);

        $targetPhase = $validated['phase'];
        $durations = $room->phase_durations ?? DetectiveRoom::DEFAULT_PHASE_DURATIONS;

        $room->phase = $targetPhase;
        $room->phase_paused_remaining = null;

        if (isset($durations[$targetPhase]) && $durations[$targetPhase] > 0) {
            $room->phase_ends_at = now()->addSeconds((int) $durations[$targetPhase]);
        } else {
            $room->phase_ends_at = null;
        }

        if ($targetPhase === 'defense' && ! $room->defense_team_id) {
            $room->defense_team_id = $room->teams()->value('id');
        }

        $room->save();
        $this->broadcastUpdate($room, 'phase_changed', ['phase' => $targetPhase]);

        return response()->json(['success' => true, 'phase' => $targetPhase]);
    }

    /**
     * Host controls the countdown timer (pause, resume, add, reduce, reset).
     */
    public function timerControl(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);

        $validated = $request->validate([
            'action' => ['required', 'in:pause,resume,add,reduce,reset'],
            'seconds' => ['nullable', 'integer', 'min:5', 'max:1800'],
        ]);

        $sec = (int) ($validated['seconds'] ?? 60);
        $remaining = $this->computeRemainingSeconds($room);

        switch ($validated['action']) {
            case 'pause':
                if ($room->phase_ends_at) {
                    $room->phase_paused_remaining = $remaining;
                    $room->phase_ends_at = null;
                }
                break;

            case 'resume':
                $dur = $room->phase_paused_remaining ?? $remaining;
                if ($dur > 0) {
                    $room->phase_ends_at = now()->addSeconds($dur);
                    $room->phase_paused_remaining = null;
                }
                break;

            case 'add':
                if ($room->phase_paused_remaining !== null) {
                    $room->phase_paused_remaining += $sec;
                } elseif ($room->phase_ends_at) {
                    $base = $room->phase_ends_at->isFuture() ? $room->phase_ends_at : now();
                    $room->phase_ends_at = $base->copy()->addSeconds($sec);
                } else {
                    $room->phase_ends_at = now()->addSeconds($sec);
                }
                break;

            case 'reduce':
                if ($room->phase_paused_remaining !== null) {
                    $room->phase_paused_remaining = max(0, $room->phase_paused_remaining - $sec);
                } elseif ($room->phase_ends_at) {
                    $newRem = max(0, $remaining - $sec);
                    $room->phase_ends_at = $newRem > 0 ? now()->addSeconds($newRem) : now();
                }
                break;

            case 'reset':
                $durations = $room->phase_durations ?? DetectiveRoom::DEFAULT_PHASE_DURATIONS;
                $defaultDur = (int) ($durations[$room->phase] ?? 0);
                $room->phase_paused_remaining = null;
                $room->phase_ends_at = $defaultDur > 0 ? now()->addSeconds($defaultDur) : null;
                break;
        }

        $room->save();
        $this->broadcastUpdate($room, 'timer_updated');

        return response()->json([
            'success' => true,
            'remaining_seconds' => $this->computeRemainingSeconds($room),
        ]);
    }

    /**
     * Host selects which team is spotlighted during Defense phase.
     */
    public function setDefenseTeam(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);
        $validated = $request->validate([
            'team_id' => ['required', 'integer'],
        ]);

        $team = DetectiveTeam::where('detective_room_id', $room->id)->where('id', $validated['team_id'])->firstOrFail();
        $room->update(['defense_team_id' => $team->id]);
        $this->broadcastUpdate($room, 'defense_team_changed');

        return response()->json(['success' => true]);
    }

    /**
     * Host atomically adjusts a team's lives (+1 / -1).
     */
    public function adjustLives(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);
        $validated = $request->validate([
            'team_id' => ['required', 'integer'],
            'delta' => ['required', 'integer', 'min:-10', 'max:10'],
        ]);

        $newLives = DB::transaction(function () use ($room, $validated): int {
            $team = DetectiveTeam::where('detective_room_id', $room->id)
                ->where('id', $validated['team_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $team->lives = max(0, $team->lives + (int) $validated['delta']);
            $team->save();

            return $team->lives;
        });

        $this->broadcastUpdate($room, 'lives_adjusted');

        return response()->json(['success' => true, 'lives' => $newLives]);
    }

    /**
     * Host broadcasts a Breaking News alert to all players and projector.
     */
    public function sendBreakingNews(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:3', 'max:600'],
        ]);

        $feed = $room->breaking_news_feed ?? [];
        $item = [
            'id' => Str::random(10),
            'message' => trim($validated['message']),
            'sent_at' => now()->toISOString(),
        ];
        array_unshift($feed, $item);

        $room->update(['breaking_news_feed' => array_slice($feed, 0, 20)]);
        $this->broadcastUpdate($room, 'breaking_news', $item);

        return response()->json(['success' => true, 'item' => $item]);
    }

    /**
     * Player submits a free-text investigation question or Expert Consultation (-1 or -2 lives atomically).
     */
    public function askQuestion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'player_token' => ['required', 'string'],
            'question_text' => ['required', 'string', 'min:5', 'max:600'],
            'is_expert_consultation' => ['nullable', 'boolean'],
        ]);

        $room = DetectiveRoom::where('code', strtoupper($validated['code']))->firstOrFail();
        $player = $this->authenticatePlayer($room, $validated['player_token']);

        if (! $player->detective_team_id) {
            return response()->json(['success' => false, 'message' => 'Kamu belum bergabung ke kelompok mana pun.'], 422);
        }

        $isExpert = (bool) ($validated['is_expert_consultation'] ?? false);
        $cost = $isExpert ? 2 : 1;

        $result = DB::transaction(function () use ($room, $player, $validated, $isExpert, $cost): array {
            $lockedRoom = DetectiveRoom::where('id', $room->id)->lockForUpdate()->firstOrFail();

            if ($lockedRoom->phase !== 'investigation') {
                return ['ok' => false, 'code' => 422, 'message' => 'Pertanyaan hanya dapat diajukan saat Phase 03 — Investigation!'];
            }

            $team = DetectiveTeam::where('id', $player->detective_team_id)->lockForUpdate()->firstOrFail();

            if ($team->submitted_at !== null) {
                return ['ok' => false, 'code' => 422, 'message' => 'Kelompokmu sudah mengumpulkan laporan akhir dan tidak dapat mengirim pertanyaan tambahan.'];
            }

            if ($team->lives < $cost) {
                return ['ok' => false, 'code' => 422, 'message' => $isExpert
                    ? 'Nyawa kelompok tidak cukup untuk Expert Consultation (butuh 2 nyawa).'
                    : 'Nyawa investigasi kelompok sudah habis! Silakan diskusikan bukti yang ada dan siapkan kesimpulan akhir.'];
            }

            $team->lives -= $cost;
            $team->questions_asked_count += 1;
            $team->save();

            $question = DetectiveQuestion::create([
                'detective_room_id' => $lockedRoom->id,
                'detective_team_id' => $team->id,
                'detective_player_id' => $player->id,
                'question_text' => trim($validated['question_text']),
                'is_expert_consultation' => $isExpert,
                'status' => 'pending',
            ]);

            return ['ok' => true, 'lives' => $team->lives, 'question' => $question];
        });

        if (! $result['ok']) {
            return response()->json(['success' => false, 'message' => $result['message']], $result['code']);
        }

        $this->broadcastUpdate($room, 'question_asked', ['team_id' => $player->detective_team_id]);

        return response()->json([
            'success' => true,
            'lives' => $result['lives'],
            'question' => $result['question'],
        ]);
    }

    /**
     * Host replies to or classifies an incoming question.
     */
    public function answerQuestion(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);

        $validated = $request->validate([
            'question_id' => ['required', 'integer'],
            'status' => ['required', 'in:answered,irrelevant,unknown,rejected_direct'],
            'answer_text' => ['nullable', 'string', 'max:1500'],
        ]);

        $question = DetectiveQuestion::where('detective_room_id', $room->id)
            ->where('id', $validated['question_id'])
            ->firstOrFail();

        $defaultMessages = [
            'irrelevant' => 'Pertanyaan ini tidak relevan dengan akar permasalahan kasus.',
            'unknown' => 'Informasi ini belum diketahui atau tidak tercatat dalam data kasus.',
            'rejected_direct' => 'Ditolak: Jangan langsung meminta jawaban akhir/root cause! Rumuskan pertanyaan investigasi berbasis fakta.',
        ];

        $answerText = trim((string) ($validated['answer_text'] ?? ''));
        if ($answerText === '' && isset($defaultMessages[$validated['status']])) {
            $answerText = $defaultMessages[$validated['status']];
        }

        if ($validated['status'] === 'answered' && $answerText === '') {
            return response()->json(['success' => false, 'message' => 'Isi jawaban terlebih dahulu!'], 422);
        }

        $question->update([
            'status' => $validated['status'],
            'answer_text' => $answerText,
            'answered_at' => now(),
        ]);

        $this->broadcastUpdate($room, 'question_answered', ['team_id' => $question->detective_team_id]);

        return response()->json(['success' => true, 'question' => $question]);
    }

    /**
     * Team members collaborate on Evidence Board in real time.
     */
    public function updateEvidenceBoard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'player_token' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'hypothesis' => ['nullable', 'string', 'max:1000'],
            'eliminated_causes' => ['nullable', 'array', 'max:30'],
            'eliminated_causes.*' => ['string', 'max:250'],
            'evidence_items' => ['nullable', 'array', 'max:40'],
            'evidence_items.*' => ['string', 'max:300'],
        ]);

        $room = DetectiveRoom::where('code', strtoupper($validated['code']))->firstOrFail();
        $player = $this->authenticatePlayer($room, $validated['player_token']);

        $team = DetectiveTeam::where('detective_room_id', $room->id)
            ->where('id', $player->detective_team_id)
            ->firstOrFail();

        if ($team->submitted_at !== null) {
            return response()->json(['success' => false, 'message' => 'Laporan akhir sudah dikumpulkan; Evidence Board terkunci.'], 422);
        }

        $updates = [];
        if (array_key_exists('notes', $validated)) {
            $updates['notes'] = $validated['notes'];
        }
        if (array_key_exists('hypothesis', $validated)) {
            $updates['hypothesis'] = $validated['hypothesis'];
        }
        if (array_key_exists('eliminated_causes', $validated)) {
            $updates['eliminated_causes'] = $validated['eliminated_causes'];
        }
        if (array_key_exists('evidence_items', $validated)) {
            $updates['evidence_items'] = $validated['evidence_items'];
        }

        $team->update($updates);
        $this->broadcastUpdate($room, 'board_updated', ['team_id' => $team->id]);

        return response()->json(['success' => true]);
    }

    /**
     * Team submits Final Deduction Report (locks additional questions).
     */
    public function submitFinalReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'player_token' => ['required', 'string'],
            'initial_hypothesis' => ['required', 'string', 'max:1000'],
            'evidence_collected' => ['required', 'string', 'max:2000'],
            'eliminated_possibilities' => ['required', 'string', 'max:1500'],
            'root_cause' => ['required', 'string', 'max:1000'],
            'proposed_solution' => ['required', 'string', 'max:1500'],
            'validation_plan' => ['required', 'string', 'max:1500'],
            'confidence_level' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $room = DetectiveRoom::where('code', strtoupper($validated['code']))->firstOrFail();
        $player = $this->authenticatePlayer($room, $validated['player_token']);

        if (! in_array($room->phase, ['investigation', 'deduction'], true)) {
            return response()->json(['success' => false, 'message' => 'Pengumpulan laporan akhir hanya dibuka saat Investigation atau Final Deduction!'], 422);
        }

        $result = DB::transaction(function () use ($room, $player, $validated): array {
            $team = DetectiveTeam::where('detective_room_id', $room->id)
                ->where('id', $player->detective_team_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($team->submitted_at !== null) {
                return ['ok' => false, 'message' => 'Laporan akhir kelompok sudah pernah dikumpulkan!'];
            }

            $team->update([
                'final_report' => [
                    'initial_hypothesis' => trim($validated['initial_hypothesis']),
                    'evidence_collected' => trim($validated['evidence_collected']),
                    'eliminated_possibilities' => trim($validated['eliminated_possibilities']),
                    'root_cause' => trim($validated['root_cause']),
                    'proposed_solution' => trim($validated['proposed_solution']),
                    'validation_plan' => trim($validated['validation_plan']),
                    'confidence_level' => (int) $validated['confidence_level'],
                    'submitted_by' => $player->name,
                ],
                'submitted_at' => now(),
            ]);

            return ['ok' => true];
        });

        if (! $result['ok']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        $this->broadcastUpdate($room, 'report_submitted', ['team_id' => $player->detective_team_id]);

        return response()->json(['success' => true, 'message' => 'Laporan investigasi akhir berhasil dikumpulkan!']);
    }

    /**
     * Host scores a team (20 + 30 + 20 + 20 + 10 = 100 max) and assigns special awards.
     */
    public function scoreTeam(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);

        $validated = $request->validate([
            'team_id' => ['required', 'integer'],
            'question_quality' => ['required', 'integer', 'min:0', 'max:20'],
            'root_cause_accuracy' => ['required', 'integer', 'min:0', 'max:30'],
            'evidence_strength' => ['required', 'integer', 'min:0', 'max:20'],
            'solution_effectiveness' => ['required', 'integer', 'min:0', 'max:20'],
            'efficiency' => ['required', 'integer', 'min:0', 'max:10'],
            'awards' => ['nullable', 'array'],
            'awards.*' => ['string', 'max:60'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $team = DetectiveTeam::where('detective_room_id', $room->id)
            ->where('id', $validated['team_id'])
            ->firstOrFail();

        $total = (int) $validated['question_quality']
            + (int) $validated['root_cause_accuracy']
            + (int) $validated['evidence_strength']
            + (int) $validated['solution_effectiveness']
            + (int) $validated['efficiency'];

        $team->update([
            'scores' => [
                'question_quality' => (int) $validated['question_quality'],
                'root_cause_accuracy' => (int) $validated['root_cause_accuracy'],
                'evidence_strength' => (int) $validated['evidence_strength'],
                'solution_effectiveness' => (int) $validated['solution_effectiveness'],
                'efficiency' => (int) $validated['efficiency'],
                'feedback' => trim((string) ($validated['feedback'] ?? '')),
            ],
            'total_score' => min(100, $total),
            'awards' => $validated['awards'] ?? [],
        ]);

        $this->broadcastUpdate($room, 'team_scored', ['team_id' => $team->id]);

        return response()->json(['success' => true, 'total_score' => $team->total_score]);
    }

    /**
     * Host kicks a player from the room.
     */
    public function kickPlayer(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);
        $validated = $request->validate([
            'player_id' => ['required', 'integer'],
        ]);

        DetectivePlayer::where('detective_room_id', $room->id)
            ->where('id', $validated['player_id'])
            ->delete();

        $this->broadcastUpdate($room, 'player_kicked');

        return response()->json(['success' => true]);
    }

    /**
     * Host resets the room back to lobby for a fresh round/case.
     */
    public function resetRoom(Request $request): JsonResponse
    {
        $room = $this->requireHostRoom($request);

        DB::transaction(function () use ($room): void {
            $room->update([
                'phase' => 'lobby',
                'phase_ends_at' => null,
                'phase_paused_remaining' => null,
                'defense_team_id' => null,
                'breaking_news_feed' => [],
            ]);

            DetectiveQuestion::where('detective_room_id', $room->id)->delete();

            DetectiveTeam::where('detective_room_id', $room->id)->update([
                'lives' => $room->default_lives,
                'questions_asked_count' => 0,
                'notes' => null,
                'hypothesis' => null,
                'eliminated_causes' => null,
                'evidence_items' => null,
                'final_report' => null,
                'submitted_at' => null,
                'scores' => null,
                'total_score' => 0,
                'awards' => null,
            ]);

            DetectivePlayer::where('detective_room_id', $room->id)->update(['is_ready' => false]);
        });

        $this->broadcastUpdate($room, 'room_reset');

        return response()->json(['success' => true]);
    }

    // =========================================================================
    // CUSTOM CASE BUILDER (HOST AUTHORIZED)
    // =========================================================================

    public function listCases(Request $request): JsonResponse
    {
        DetectiveCaseLibraryService::ensureDefaultCasesExist();
        $cases = DetectiveCase::orderBy('is_default', 'desc')->orderBy('id')->get();

        $code = strtoupper((string) $request->query('code', ''));
        $hostToken = (string) ($request->header('X-Host-Token') ?? $request->query('host_token', ''));

        $isHost = false;
        if ($code !== '' && $hostToken !== '') {
            $room = DetectiveRoom::where('code', $code)->first();
            $isHost = $room && hash_equals($room->host_token, $hostToken);
        }

        return response()->json([
            'success' => true,
            'cases' => $isHost ? $cases : $cases->map->toPublicArray(),
        ]);
    }

    public function storeCase(Request $request): JsonResponse
    {
        $this->requireHostRoom($request);
        $data = $this->validateCasePayload($request);
        $data['slug'] = $this->uniqueCaseSlug($data['title']);
        $data['is_default'] = false;

        $case = DetectiveCase::create($data);

        return response()->json(['success' => true, 'case' => $case]);
    }

    public function updateCase(Request $request, DetectiveCase $detectiveCase): JsonResponse
    {
        $this->requireHostRoom($request);
        $data = $this->validateCasePayload($request);
        $detectiveCase->update($data);

        return response()->json(['success' => true, 'case' => $detectiveCase->fresh()]);
    }

    public function duplicateCase(Request $request, DetectiveCase $detectiveCase): JsonResponse
    {
        $this->requireHostRoom($request);
        $copy = $detectiveCase->replicate();
        $copy->title = $detectiveCase->title.' (Copy)';
        $copy->slug = $this->uniqueCaseSlug($copy->title);
        $copy->is_default = false;
        $copy->save();

        return response()->json(['success' => true, 'case' => $copy]);
    }

    public function deleteCase(Request $request, DetectiveCase $detectiveCase): JsonResponse
    {
        $this->requireHostRoom($request);

        if (DetectiveCase::count() <= 1) {
            return response()->json(['success' => false, 'message' => 'Minimal harus ada 1 kasus tersisa di database.'], 422);
        }

        $detectiveCase->delete();

        return response()->json(['success' => true]);
    }

    // =========================================================================
    // INTERNAL HELPERS & STATE FORMATTERS
    // =========================================================================

    protected function createDefaultRoom(): DetectiveRoom
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (DetectiveRoom::where('code', $code)->exists());

        $firstCase = DetectiveCase::orderBy('id')->first();

        $room = DetectiveRoom::create([
            'code' => $code,
            'host_token' => Str::random(48),
            'detective_case_id' => $firstCase?->id,
            'phase' => 'lobby',
            'investigation_mode' => 'private',
            'target_players' => 17,
            'default_lives' => 10,
            'phase_durations' => DetectiveRoom::DEFAULT_PHASE_DURATIONS,
            'breaking_news_feed' => [],
        ]);

        // Default 5 kelompok: Alpha (4), Bravo (4), Charlie (3), Delta (3), Echo (3) = 17 pemain
        $defaultTeams = [
            ['name' => 'Team Alpha', 'max_capacity' => 4],
            ['name' => 'Team Bravo', 'max_capacity' => 4],
            ['name' => 'Team Charlie', 'max_capacity' => 3],
            ['name' => 'Team Delta', 'max_capacity' => 3],
            ['name' => 'Team Echo', 'max_capacity' => 3],
        ];

        foreach ($defaultTeams as $idx => $t) {
            DetectiveTeam::create([
                'detective_room_id' => $room->id,
                'name' => $t['name'],
                'max_capacity' => $t['max_capacity'],
                'sort_order' => $idx + 1,
                'lives' => $room->default_lives,
            ]);
        }

        return $room->load(['teams.players', 'detectiveCase']);
    }

    protected function requireHostRoom(Request $request): DetectiveRoom
    {
        $code = strtoupper((string) ($request->input('code') ?? $request->query('code', '')));
        $token = (string) ($request->header('X-Host-Token') ?? $request->input('host_token', ''));

        $room = DetectiveRoom::where('code', $code)->first();

        if (! $room || $token === '' || ! hash_equals($room->host_token, $token)) {
            abort(response()->json(['success' => false, 'message' => 'Unauthorized Host access.'], 403));
        }

        return $room;
    }

    protected function authenticatePlayer(DetectiveRoom $room, string $token): DetectivePlayer
    {
        $player = DetectivePlayer::where('detective_room_id', $room->id)
            ->where('player_token', $token)
            ->first();

        if (! $player) {
            abort(response()->json(['success' => false, 'message' => 'Sesi pemain tidak valid atau sudah dikeluarkan.'], 403));
        }

        return $player;
    }

    protected function computeRemainingSeconds(DetectiveRoom $room): int
    {
        if ($room->phase_paused_remaining !== null) {
            return max(0, (int) $room->phase_paused_remaining);
        }

        if ($room->phase_ends_at) {
            return max(0, (int) now()->diffInSeconds($room->phase_ends_at, false));
        }

        return 0;
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateCasePayload(Request $request): array
    {
        $v = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:3000'],
            'category' => ['required', 'string', 'max:80'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'initial_assumption' => ['required', 'string', 'max:1500'],
            'mission_objectives' => ['required', 'array', 'min:1'],
            'mission_objectives.*' => ['string', 'max:500'],
            'hidden_facts' => ['required', 'array', 'min:1'],
            'hidden_facts.*' => ['string', 'max:500'],
            'evidence' => ['required', 'array', 'min:1'],
            'evidence.*' => ['string', 'max:500'],
            'false_leads' => ['nullable', 'array'],
            'false_leads.*' => ['string', 'max:500'],
            'special_hints' => ['nullable', 'array'],
            'special_hints.*' => ['string', 'max:500'],
            'breaking_news' => ['nullable', 'array'],
            'breaking_news.*' => ['string', 'max:500'],
            'root_cause' => ['required', 'string', 'max:1500'],
            'recommended_solutions' => ['required', 'array', 'min:1'],
            'recommended_solutions.*' => ['string', 'max:500'],
            'validation_method' => ['required', 'string', 'max:1500'],
            'default_duration' => ['nullable', 'integer', 'min:5', 'max:120'],
        ]);

        $v['default_duration'] = (int) ($v['default_duration'] ?? 20);

        return $v;
    }

    protected function uniqueCaseSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'case';
        $slug = $base;
        $counter = 2;

        while (DetectiveCase::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatRoomForHost(DetectiveRoom $room): array
    {
        $case = $room->detectiveCase;

        return [
            'id' => $room->id,
            'code' => $room->code,
            'phase' => $room->phase,
            'investigation_mode' => $room->investigation_mode,
            'target_players' => $room->target_players,
            'default_lives' => $room->default_lives,
            'phase_durations' => $room->phase_durations ?? DetectiveRoom::DEFAULT_PHASE_DURATIONS,
            'is_paused' => $room->phase_paused_remaining !== null,
            'defense_team_id' => $room->defense_team_id,
            'breaking_news_feed' => $room->breaking_news_feed ?? [],
            'total_players' => $room->players->count(),
            'case' => $case ? [
                ...$case->toTruthRevealArray(),
                'special_hints' => $case->special_hints ?? [],
                'breaking_news' => $case->breaking_news ?? [],
            ] : null,
            'teams' => $room->teams->map(fn (DetectiveTeam $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'max_capacity' => $t->max_capacity,
                'lives' => $t->lives,
                'questions_asked_count' => $t->questions_asked_count,
                'submitted_at' => $t->submitted_at?->toISOString(),
                'final_report' => $t->final_report,
                'scores' => $t->scores,
                'total_score' => $t->total_score,
                'awards' => $t->awards ?? [],
                'players' => $t->players->map(fn (DetectivePlayer $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'is_ready' => $p->is_ready,
                ]),
            ]),
            'questions' => $room->questions->map(fn (DetectiveQuestion $q) => [
                'id' => $q->id,
                'team_id' => $q->detective_team_id,
                'team_name' => $q->team?->name ?? '-',
                'player_name' => $q->player?->name ?? 'Anonim',
                'question_text' => $q->question_text,
                'is_expert_consultation' => $q->is_expert_consultation,
                'status' => $q->status,
                'answer_text' => $q->answer_text,
                'created_at' => $q->created_at?->format('H:i:s'),
                'answered_at' => $q->answered_at?->format('H:i:s'),
            ]),
        ];
    }

    /**
     * Format strictly sanitized room state for Player client (NO SECRET CASE INFO).
     *
     * @return array<string, mixed>
     */
    protected function formatRoomForPlayer(DetectiveRoom $room, ?DetectivePlayer $player): array
    {
        $case = $room->detectiveCase;
        $revealAllowed = in_array($room->phase, ['reveal', 'leaderboard'], true);
        $defenseAllowed = in_array($room->phase, ['defense', 'reveal', 'leaderboard'], true);

        $myTeam = $player ? $room->teams->firstWhere('id', $player->detective_team_id) : null;

        // Filter questions: Private mode restricts players to their own team until defense/reveal
        $questions = $room->questions->filter(function (DetectiveQuestion $q) use ($room, $myTeam, $defenseAllowed) {
            if ($room->investigation_mode === 'public' || $defenseAllowed) {
                return true;
            }

            return $myTeam && $q->detective_team_id === $myTeam->id;
        })->values()->map(fn (DetectiveQuestion $q) => [
            'id' => $q->id,
            'team_id' => $q->detective_team_id,
            'team_name' => $q->team?->name ?? '-',
            'player_name' => $q->player?->name ?? 'Detective',
            'question_text' => $q->question_text,
            'is_expert_consultation' => $q->is_expert_consultation,
            'status' => $q->status,
            'answer_text' => $q->answer_text,
            'created_at' => $q->created_at?->format('H:i:s'),
        ]);

        return [
            'code' => $room->code,
            'phase' => $room->phase,
            'investigation_mode' => $room->investigation_mode,
            'target_players' => $room->target_players,
            'default_lives' => $room->default_lives,
            'is_paused' => $room->phase_paused_remaining !== null,
            'defense_team_id' => $room->defense_team_id,
            'breaking_news_feed' => $room->breaking_news_feed ?? [],
            'total_players' => $room->players->count(),
            'case' => $case ? ($revealAllowed ? $case->toTruthRevealArray() : $case->toPublicArray()) : null,
            'me' => $player ? [
                'id' => $player->id,
                'name' => $player->name,
                'team_id' => $player->detective_team_id,
                'is_ready' => $player->is_ready,
            ] : null,
            'my_team' => $myTeam ? [
                'id' => $myTeam->id,
                'name' => $myTeam->name,
                'lives' => $myTeam->lives,
                'questions_asked_count' => $myTeam->questions_asked_count,
                'notes' => $myTeam->notes ?? '',
                'hypothesis' => $myTeam->hypothesis ?? '',
                'eliminated_causes' => $myTeam->eliminated_causes ?? [],
                'evidence_items' => $myTeam->evidence_items ?? [],
                'final_report' => $myTeam->final_report,
                'submitted_at' => $myTeam->submitted_at?->toISOString(),
                'total_score' => $myTeam->total_score,
                'scores' => $myTeam->scores,
                'awards' => $myTeam->awards ?? [],
            ] : null,
            'teams' => $room->teams->map(fn (DetectiveTeam $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'max_capacity' => $t->max_capacity,
                'lives' => $t->lives,
                'questions_asked_count' => $t->questions_asked_count,
                'submitted' => $t->submitted_at !== null,
                'final_report' => ($defenseAllowed || ($myTeam && $t->id === $myTeam->id)) ? $t->final_report : null,
                'total_score' => $room->phase === 'leaderboard' ? $t->total_score : null,
                'awards' => $room->phase === 'leaderboard' ? ($t->awards ?? []) : [],
                'players' => $t->players->map(fn (DetectivePlayer $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'is_ready' => $p->is_ready,
                ]),
            ]),
            'questions' => $questions,
        ];
    }

    /**
     * Format strictly sanitized room state for Classroom Projector Mode (NO SECRET CASE INFO).
     *
     * @return array<string, mixed>
     */
    protected function formatRoomForProjector(DetectiveRoom $room): array
    {
        $case = $room->detectiveCase;
        $revealAllowed = in_array($room->phase, ['reveal', 'leaderboard'], true);

        return [
            'code' => $room->code,
            'phase' => $room->phase,
            'investigation_mode' => $room->investigation_mode,
            'target_players' => $room->target_players,
            'default_lives' => $room->default_lives,
            'is_paused' => $room->phase_paused_remaining !== null,
            'defense_team_id' => $room->defense_team_id,
            'breaking_news_feed' => $room->breaking_news_feed ?? [],
            'total_players' => $room->players->count(),
            'case' => $case ? ($revealAllowed ? $case->toTruthRevealArray() : $case->toPublicArray()) : null,
            'teams' => $room->teams->map(function (DetectiveTeam $t) use ($room, $revealAllowed) {
                $showReport = $revealAllowed || ($room->phase === 'defense' && $room->defense_team_id === $t->id);

                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'max_capacity' => $t->max_capacity,
                    'lives' => $t->lives,
                    'questions_asked_count' => $t->questions_asked_count,
                    'pending_questions_count' => $room->questions->where('detective_team_id', $t->id)->where('status', 'pending')->count(),
                    'submitted' => $t->submitted_at !== null,
                    'final_report' => $showReport ? $t->final_report : null,
                    'total_score' => $t->total_score,
                    'scores' => $room->phase === 'leaderboard' ? $t->scores : null,
                    'awards' => $t->awards ?? [],
                    'players' => $t->players->map(fn (DetectivePlayer $p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'is_ready' => $p->is_ready,
                    ]),
                ];
            }),
            // Only show public Q&A feed on projector if room is in Public Investigation mode
            'public_questions' => $room->investigation_mode === 'public'
                ? $room->questions->whereIn('status', ['answered', 'irrelevant', 'unknown', 'rejected_direct'])
                    ->values()
                    ->map(fn (DetectiveQuestion $q) => [
                        'id' => $q->id,
                        'team_name' => $q->team?->name ?? '-',
                        'question_text' => $q->question_text,
                        'status' => $q->status,
                        'answer_text' => $q->answer_text,
                    ])
                : [],
        ];
    }

    /**
     * Broadcast state change safely (falls back cleanly to HTTP polling when Reverb is not running).
     *
     * @param  array<string, mixed>  $payload
     */
    protected function broadcastUpdate(DetectiveRoom $room, string $type, array $payload = []): void
    {
        try {
            broadcast(new DetectiveRoomUpdated($room->code, $type, $payload));
        } catch (\Throwable) {
            // Polling handles sync reliably even without active WebSocket daemon
        }
    }
}

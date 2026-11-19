<?php

namespace App\Http\Controllers;

use App\Events\TwinGameStateChanged;
use App\Models\TwinGame;
use App\Models\TwinPlayer;
use App\Models\TwinScan;
use App\Models\TwinVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiapaKembarankuController extends Controller
{
    /**
     * Display the Host screen.
     */
    public function host(Request $request): View
    {
        $code = $request->query('code');
        $game = null;

        if ($code) {
            $game = TwinGame::with(['players' => function ($q) {
                $q->orderBy('id', 'asc');
            }])->where('code', strtoupper($code))->first();
        }

        if (! $game) {
            // Find latest active or create a new one
            $game = TwinGame::with(['players' => function ($q) {
                $q->orderBy('id', 'asc');
            }])->whereIn('status', ['lobby', 'discussion', 'voting', 'reveal'])
                ->latest()
                ->first();

            if (! $game) {
                $game = $this->createNewGame();
            }
        }

        return view('games.siapa-kembaranku.host', [
            'game' => $game,
            'logos' => TwinGame::LOGOS,
            'logoNames' => TwinGame::LOGO_NAMES,
        ]);
    }

    /**
     * Display the Player screen (mobile-first).
     */
    public function play(Request $request): View
    {
        $code = $request->query('code', '');

        return view('games.siapa-kembaranku.play', [
            'initialCode' => strtoupper($code),
            'logos' => TwinGame::LOGOS,
            'logoNames' => TwinGame::LOGO_NAMES,
        ]);
    }

    /**
     * Create a new game room.
     */
    public function createGame(Request $request): JsonResponse
    {
        $game = $this->createNewGame();

        return response()->json([
            'success' => true,
            'game' => $game,
        ]);
    }

    /**
     * Player joins a game.
     */
    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'username' => ['required', 'string', 'min:2', 'max:20'],
        ]);

        $code = strtoupper($validated['code']);
        $game = TwinGame::where('code', $code)->first();

        if (! $game) {
            return response()->json([
                'success' => false,
                'message' => 'Kode room tidak ditemukan!',
            ], 404);
        }

        if ($game->status !== 'lobby') {
            return response()->json([
                'success' => false,
                'message' => 'Game sudah dimulai, tidak bisa bergabung ke room ini!',
            ], 422);
        }

        $trimmedUsername = trim($validated['username']);

        // Check if username already used
        $exists = TwinPlayer::where('twin_game_id', $game->id)
            ->where('username', $trimmedUsername)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Nama "'.$trimmedUsername.'" sudah dipakai di room ini. Gunakan nama lain ya!',
            ], 422);
        }

        $player = TwinPlayer::create([
            'twin_game_id' => $game->id,
            'username' => $trimmedUsername,
            'player_token' => Str::random(32),
            'is_kicked' => false,
            'is_winner' => false,
        ]);

        $this->broadcastStateChange($game, 'player_joined', [
            'player_id' => $player->id,
            'username' => $player->username,
            'player_count' => $game->players()->count(),
        ]);

        return response()->json([
            'success' => true,
            'player_token' => $player->player_token,
            'player_id' => $player->id,
            'username' => $player->username,
            'game_code' => $game->code,
        ]);
    }

    /**
     * Host starts the game: assigns logos and starts discussion timer.
     */
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'timer_seconds' => ['nullable', 'integer', 'min:30', 'max:900'],
            'tie_break_rule' => ['nullable', 'string', 'in:kick_all,random'],
        ]);

        $game = TwinGame::where('code', strtoupper($validated['code']))->firstOrFail();

        $activePlayers = $game->players()->where('is_kicked', false)->get();
        $playerCount = $activePlayers->count();

        if ($playerCount < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Pemain belum cukup! Minimal 3 orang untuk memulai game.',
            ], 422);
        }

        $timerSeconds = $validated['timer_seconds'] ?? $game->timer_seconds ?? 300;
        $tieBreakRule = $validated['tie_break_rule'] ?? $game->tie_break_rule ?? 'kick_all';

        // Distribute logos ensuring every chosen logo is held by >= 2 players
        $this->distributeLogos($activePlayers);

        $game->update([
            'status' => 'discussion',
            'current_round' => 1,
            'timer_seconds' => $timerSeconds,
            'timer_ends_at' => Carbon::now()->addSeconds($timerSeconds),
            'tie_break_rule' => $tieBreakRule,
            'eliminated_player_ids' => null,
            'last_reveal_data' => null,
        ]);

        $this->broadcastStateChange($game, 'game_started', [
            'round' => 1,
            'timer_seconds' => $timerSeconds,
            'timer_ends_at' => $game->timer_ends_at?->toISOString(),
            'active_player_count' => $playerCount,
        ]);

        return response()->json([
            'success' => true,
            'game' => $this->formatGameStateForHost($game),
        ]);
    }

    /**
     * Player scans another player's QR code.
     * SECURITY RULE: Never return the scanner's own logo!
     */
    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scanner_token' => ['required', 'string'],
            'target_token' => ['required', 'string'],
        ]);

        $scanner = TwinPlayer::where('player_token', $validated['scanner_token'])->first();
        $target = TwinPlayer::where('player_token', $validated['target_token'])->first();

        if (! $scanner || ! $target) {
            return response()->json([
                'success' => false,
                'message' => 'Data QR tidak valid atau pemain tidak ditemukan!',
            ], 404);
        }

        if ($scanner->twin_game_id !== $target->twin_game_id) {
            return response()->json([
                'success' => false,
                'message' => 'Pemain ini berasal dari room game yang berbeda!',
            ], 422);
        }

        // STRICT SECURITY CHECK: Scanner CANNOT scan themselves!
        if ($scanner->id === $target->id) {
            return response()->json([
                'success' => false,
                'message' => 'Eits! Kamu tidak bisa scan QR kamu sendiri!',
            ], 422);
        }

        if ($scanner->is_kicked) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah tereliminasi di ronde sebelumnya.',
            ], 403);
        }

        $game = $scanner->game;

        if ($game->status !== 'discussion' && $game->status !== 'voting') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi scan belum dimulai atau sudah selesai.',
            ], 422);
        }

        // Record scan
        TwinScan::updateOrCreate(
            [
                'twin_game_id' => $game->id,
                'round_number' => $game->current_round,
                'scanner_player_id' => $scanner->id,
                'scanned_player_id' => $target->id,
            ],
            [
                'scanned_logo' => $target->secret_logo,
            ]
        );

        $this->broadcastStateChange($game, 'scan_recorded', [
            'scanner_id' => $scanner->id,
            'scanner_name' => $scanner->username,
            'scanned_name' => $target->username,
        ]);

        return response()->json([
            'success' => true,
            'target_id' => $target->id,
            'target_username' => $target->username,
            'target_logo' => $target->secret_logo,
            'logo_name' => TwinGame::LOGO_NAMES[$target->secret_logo] ?? 'Logo Rahasia',
        ]);
    }

    /**
     * Start the voting phase.
     */
    public function startVoting(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $game = TwinGame::where('code', strtoupper($validated['code']))->firstOrFail();

        $game->update([
            'status' => 'voting',
        ]);

        $this->broadcastStateChange($game, 'voting_started', [
            'round' => $game->current_round,
        ]);

        return response()->json([
            'success' => true,
            'game' => $this->formatGameStateForHost($game),
        ]);
    }

    /**
     * Player submits their 3 votes.
     * SECURITY RULE: Validated on server! Cannot vote self, no duplicate candidates.
     */
    public function vote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_token' => ['required', 'string'],
            'voted_player_ids' => ['required', 'array'],
            'voted_player_ids.*' => ['integer'],
        ]);

        $voter = TwinPlayer::where('player_token', $validated['player_token'])->first();

        if (! $voter) {
            return response()->json([
                'success' => false,
                'message' => 'Pemain tidak ditemukan!',
            ], 404);
        }

        if ($voter->is_kicked) {
            return response()->json([
                'success' => false,
                'message' => 'Pemain yang tereliminasi tidak dapat melakukan voting!',
            ], 403);
        }

        $game = $voter->game;

        if ($game->status !== 'voting') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi voting belum dibuka atau sudah ditutup!',
            ], 422);
        }

        $activeOthers = $game->players()
            ->where('is_kicked', false)
            ->where('id', '!=', $voter->id)
            ->pluck('id')
            ->toArray();

        $votedIds = array_values(array_unique($validated['voted_player_ids']));
        $maxVotesAllowed = min(3, count($activeOthers));

        if (count($votedIds) !== $maxVotesAllowed) {
            return response()->json([
                'success' => false,
                'message' => "Kamu harus memilih tepat {$maxVotesAllowed} pemain!",
            ], 422);
        }

        // Validate none is self and all exist among active players
        foreach ($votedIds as $targetId) {
            if ($targetId === $voter->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu tidak boleh memilih dirimu sendiri!',
                ], 422);
            }

            if (! in_array($targetId, $activeOthers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pilihan pemain tidak valid atau sudah tereliminasi.',
                ], 422);
            }
        }

        // Check if already voted in this round
        $alreadyVoted = TwinVote::where('twin_game_id', $game->id)
            ->where('round_number', $game->current_round)
            ->where('voter_player_id', $voter->id)
            ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah mengirimkan vote di ronde ini!',
            ], 422);
        }

        foreach ($votedIds as $targetId) {
            TwinVote::create([
                'twin_game_id' => $game->id,
                'round_number' => $game->current_round,
                'voter_player_id' => $voter->id,
                'voted_player_id' => $targetId,
            ]);
        }

        $activePlayersCount = $game->players()->where('is_kicked', false)->count();
        $distinctVotersCount = TwinVote::where('twin_game_id', $game->id)
            ->where('round_number', $game->current_round)
            ->distinct('voter_player_id')
            ->count('voter_player_id');

        $this->broadcastStateChange($game, 'vote_submitted', [
            'voter_id' => $voter->id,
            'voter_name' => $voter->username,
            'total_voted' => $distinctVotersCount,
            'total_players' => $activePlayersCount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vote kamu berhasil disimpan!',
            'total_voted' => $distinctVotersCount,
            'total_players' => $activePlayersCount,
        ]);
    }

    /**
     * Host triggers the reveal phase: tally votes, kick player(s), reveal true logos.
     */
    public function reveal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $game = TwinGame::where('code', strtoupper($validated['code']))->firstOrFail();

        $activePlayers = $game->players()->where('is_kicked', false)->get();
        $round = $game->current_round;

        // Calculate vote counts received by each active player
        $votes = TwinVote::where('twin_game_id', $game->id)
            ->where('round_number', $round)
            ->get();

        $voteCounts = [];
        foreach ($activePlayers as $p) {
            $voteCounts[$p->id] = 0;
        }

        foreach ($votes as $v) {
            if (isset($voteCounts[$v->voted_player_id])) {
                $voteCounts[$v->voted_player_id]++;
            }
        }

        // Determine player(s) with lowest votes to kick
        $minVotes = min($voteCounts);
        $lowestPlayerIds = array_keys(array_filter($voteCounts, fn ($count) => $count === $minVotes));

        $kickedIds = [];
        if (count($lowestPlayerIds) === count($activePlayers)) {
            // Everyone has the exact same vote count (e.g., all 0 or all equal)
            // Kick only 1 randomly to avoid wiping out the whole class
            $randomKey = array_rand($lowestPlayerIds);
            $kickedIds = [$lowestPlayerIds[$randomKey]];
        } elseif ($game->tie_break_rule === 'random' && count($lowestPlayerIds) > 1) {
            $randomKey = array_rand($lowestPlayerIds);
            $kickedIds = [$lowestPlayerIds[$randomKey]];
        } else {
            // kick_all tied for lowest
            $kickedIds = $lowestPlayerIds;
        }

        // Apply kick
        TwinPlayer::whereIn('id', $kickedIds)->update([
            'is_kicked' => true,
            'kick_round' => $round,
        ]);

        // Compute correct guesses for each voter
        $correctGuesses = [];
        foreach ($activePlayers as $voter) {
            $voterVotes = $votes->where('voter_player_id', $voter->id);
            $correctForThisVoter = [];
            foreach ($voterVotes as $vv) {
                $targetPlayer = $activePlayers->firstWhere('id', $vv->voted_player_id);
                if ($targetPlayer && $targetPlayer->secret_logo === $voter->secret_logo) {
                    $correctForThisVoter[] = [
                        'id' => $targetPlayer->id,
                        'username' => $targetPlayer->username,
                        'logo' => $targetPlayer->secret_logo,
                    ];
                }
            }
            $correctGuesses[$voter->id] = [
                'count' => count($correctForThisVoter),
                'twins' => $correctForThisVoter,
            ];
        }

        // Check if finished (<= 3 remaining active players)
        $remainingActive = TwinPlayer::where('twin_game_id', $game->id)
            ->where('is_kicked', false)
            ->get();

        $isFinished = $remainingActive->count() <= 3;
        if ($isFinished) {
            TwinPlayer::whereIn('id', $remainingActive->pluck('id'))->update([
                'is_winner' => true,
            ]);
            $game->status = 'finished';
        } else {
            $game->status = 'reveal';
        }

        // Build players reveal data for host screen
        $playersReveal = $activePlayers->map(function ($p) use ($voteCounts, $kickedIds, $correctGuesses) {
            return [
                'id' => $p->id,
                'username' => $p->username,
                'secret_logo' => $p->secret_logo,
                'logo_name' => TwinGame::LOGO_NAMES[$p->secret_logo] ?? '',
                'votes_received' => $voteCounts[$p->id] ?? 0,
                'is_kicked' => in_array($p->id, $kickedIds),
                'correct_guesses' => $correctGuesses[$p->id]['count'] ?? 0,
            ];
        })->values()->toArray();

        $revealData = [
            'round' => $round,
            'players' => $playersReveal,
            'kicked_ids' => $kickedIds,
            'kicked_players' => $activePlayers->whereIn('id', $kickedIds)->values()->map(fn ($p) => [
                'id' => $p->id,
                'username' => $p->username,
                'logo' => $p->secret_logo,
                'votes' => $voteCounts[$p->id] ?? 0,
            ])->toArray(),
            'is_finished' => $isFinished,
            'winners' => $isFinished ? $remainingActive->map(fn ($w) => [
                'id' => $w->id,
                'username' => $w->username,
                'logo' => $w->secret_logo,
            ])->values()->toArray() : [],
        ];

        $game->eliminated_player_ids = $kickedIds;
        $game->last_reveal_data = $revealData;
        $game->save();

        $this->broadcastStateChange($game, 'round_revealed', $revealData);

        return response()->json([
            'success' => true,
            'reveal' => $revealData,
        ]);
    }

    /**
     * Start next round with remaining active players (re-shuffle logos).
     */
    public function nextRound(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $game = TwinGame::where('code', strtoupper($validated['code']))->firstOrFail();

        $activePlayers = $game->players()->where('is_kicked', false)->get();

        if ($activePlayers->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Pemain aktif kurang dari 2 orang, permainan berakhir!',
            ], 422);
        }

        // Re-shuffle logos among remaining players
        $this->distributeLogos($activePlayers);

        $nextRoundNum = $game->current_round + 1;
        $game->update([
            'status' => 'discussion',
            'current_round' => $nextRoundNum,
            'timer_ends_at' => Carbon::now()->addSeconds($game->timer_seconds),
            'eliminated_player_ids' => null,
            'last_reveal_data' => null,
        ]);

        $this->broadcastStateChange($game, 'next_round', [
            'round' => $nextRoundNum,
            'timer_seconds' => $game->timer_seconds,
            'timer_ends_at' => $game->timer_ends_at?->toISOString(),
            'active_player_count' => $activePlayers->count(),
        ]);

        return response()->json([
            'success' => true,
            'game' => $this->formatGameStateForHost($game),
        ]);
    }

    /**
     * Polling / Current Game State endpoint.
     */
    public function state(Request $request): JsonResponse
    {
        $code = strtoupper($request->query('code', ''));
        $token = $request->query('token');

        $game = TwinGame::where('code', $code)->first();

        if (! $game) {
            return response()->json([
                'success' => false,
                'message' => 'Game room tidak ditemukan.',
            ], 404);
        }

        // If queried by a player with player_token
        if ($token) {
            $player = TwinPlayer::where('player_token', $token)
                ->where('twin_game_id', $game->id)
                ->first();

            if (! $player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi pemain tidak valid.',
                ], 403);
            }

            // Other active players that this player can vote for (NEVER exposes secret_logo!)
            $otherActivePlayers = TwinPlayer::where('twin_game_id', $game->id)
                ->where('is_kicked', false)
                ->where('id', '!=', $player->id)
                ->select(['id', 'username'])
                ->get();

            // Scans made by this player in this round
            $scannedList = TwinScan::with('scanned:id,username')
                ->where('twin_game_id', $game->id)
                ->where('round_number', $game->current_round)
                ->where('scanner_player_id', $player->id)
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->scanned_player_id,
                    'username' => $s->scanned?->username,
                    'logo' => $s->scanned_logo,
                    'logo_name' => TwinGame::LOGO_NAMES[$s->scanned_logo] ?? '',
                ]);

            // Check if player has voted this round
            $hasVoted = TwinVote::where('twin_game_id', $game->id)
                ->where('round_number', $game->current_round)
                ->where('voter_player_id', $player->id)
                ->exists();

            return response()->json([
                'success' => true,
                'role' => 'player',
                'game' => [
                    'code' => $game->code,
                    'status' => $game->status,
                    'current_round' => $game->current_round,
                    'timer_seconds' => $game->timer_seconds,
                    'timer_ends_at' => $game->timer_ends_at?->toISOString(),
                ],
                'player' => [
                    'id' => $player->id,
                    'username' => $player->username,
                    'player_token' => $player->player_token,
                    'is_kicked' => (bool) $player->is_kicked,
                    'is_winner' => (bool) $player->is_winner,
                    'kick_round' => $player->kick_round,
                    // NOTICE: secret_logo is NOT included here! Kept strictly secure.
                ],
                'other_active_players' => $otherActivePlayers,
                'scanned_list' => $scannedList,
                'has_voted' => $hasVoted,
                'reveal' => $game->status === 'reveal' || $game->status === 'finished' ? $game->last_reveal_data : null,
            ]);
        }

        // Host view state
        return response()->json([
            'success' => true,
            'role' => 'host',
            'game' => $this->formatGameStateForHost($game),
        ]);
    }

    /**
     * Distribute logos to players ensuring every logo is held by >= 2 players.
     *
     * @param  Collection<int, TwinPlayer>  $players
     */
    protected function distributeLogos($players): void
    {
        $count = $players->count();
        if ($count < 2) {
            return;
        }

        // Available emojis
        $allLogos = TwinGame::LOGOS;
        shuffle($allLogos);

        // Determine how many distinct logos we can have
        // Each distinct logo must have at least 2 holders
        $maxDistinct = max(1, (int) floor($count / 2));
        $chosenLogos = array_slice($allLogos, 0, min($maxDistinct, count($allLogos)));
        $numLogos = count($chosenLogos);

        // Assign 2 of each chosen logo first
        $assignedLogos = [];
        foreach ($chosenLogos as $l) {
            $assignedLogos[] = $l;
            $assignedLogos[] = $l;
        }

        // Distribute remaining slots randomly among the same chosen logos
        $remainingCount = $count - count($assignedLogos);
        for ($i = 0; $i < $remainingCount; $i++) {
            $assignedLogos[] = $chosenLogos[$i % $numLogos];
        }

        // Shuffle assignment
        shuffle($assignedLogos);

        // Save to players
        foreach ($players as $index => $player) {
            $player->secret_logo = $assignedLogos[$index];
            $player->save();
        }
    }

    /**
     * Create a fresh game instance.
     */
    protected function createNewGame(): TwinGame
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (TwinGame::where('code', $code)->exists());

        return TwinGame::create([
            'code' => $code,
            'status' => 'lobby',
            'current_round' => 1,
            'timer_seconds' => 300,
            'tie_break_rule' => 'kick_all',
        ]);
    }

    /**
     * Format game state for the host screen.
     *
     * @return array<string, mixed>
     */
    protected function formatGameStateForHost(TwinGame $game): array
    {
        $players = $game->players()->get()->map(function ($p) use ($game) {
            $data = [
                'id' => $p->id,
                'username' => $p->username,
                'is_kicked' => (bool) $p->is_kicked,
                'is_winner' => (bool) $p->is_winner,
            ];

            // Only expose secret_logo on host if in reveal or finished phase
            if ($game->status === 'reveal' || $game->status === 'finished') {
                $data['secret_logo'] = $p->secret_logo;
                $data['logo_name'] = TwinGame::LOGO_NAMES[$p->secret_logo] ?? '';
            }

            return $data;
        });

        $activeCount = $game->players()->where('is_kicked', false)->count();
        $votedCount = TwinVote::where('twin_game_id', $game->id)
            ->where('round_number', $game->current_round)
            ->distinct('voter_player_id')
            ->count('voter_player_id');

        return [
            'id' => $game->id,
            'code' => $game->code,
            'status' => $game->status,
            'current_round' => $game->current_round,
            'timer_seconds' => $game->timer_seconds,
            'timer_ends_at' => $game->timer_ends_at?->toISOString(),
            'tie_break_rule' => $game->tie_break_rule,
            'players' => $players,
            'active_players_count' => $activeCount,
            'voted_players_count' => $votedCount,
            'last_reveal_data' => $game->last_reveal_data,
        ];
    }

    /**
     * Broadcast state change event to Reverb / WebSockets.
     *
     * @param  array<string, mixed>  $payload
     */
    protected function broadcastStateChange(TwinGame $game, string $type, array $payload = []): void
    {
        try {
            broadcast(new TwinGameStateChanged($game->code, $type, $payload));
        } catch (\Throwable) {
            // Ignore broadcast failures so local development without Reverb running still works via HTTP polling!
        }
    }
}

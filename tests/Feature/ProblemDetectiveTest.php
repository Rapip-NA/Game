<?php

namespace Tests\Feature;

use App\Models\DetectiveCase;
use App\Models\DetectiveRoom;
use App\Models\GameMaintenance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemDetectiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_hub_host_play_and_projector_pages_render_and_seed_ten_cases(): void
    {
        $this->get('/games/problem-detective')->assertStatus(200)->assertSee('THE PROBLEM DETECTIVE');
        $this->get('/problem-detective')->assertStatus(200);
        $this->get('/games/problem-detective/host')->assertStatus(200)->assertSee('Antrean Pertanyaan Investigasi');
        $this->get('/games/problem-detective/play')->assertStatus(200)->assertSee('Gabung Investigasi');
        $this->get('/games/problem-detective/projector')->assertStatus(200)->assertSee('Projector Mode');

        $this->assertGreaterThanOrEqual(10, DetectiveCase::count());
        foreach (DetectiveCase::all() as $case) {
            $this->assertGreaterThanOrEqual(10, count($case->hidden_facts ?? []));
        }
    }

    public function test_room_creation_has_default_five_teams_for_seventeen_players(): void
    {
        $response = $this->postJson('/games/problem-detective/rooms');
        $response->assertStatus(200)->assertJson(['success' => true]);

        $code = $response->json('code');
        $room = DetectiveRoom::with('teams')->where('code', $code)->first();

        $this->assertNotNull($room);
        $this->assertEquals('private', $room->investigation_mode);
        $this->assertEquals(10, $room->default_lives);
        $this->assertCount(5, $room->teams);
        $this->assertEquals(17, $room->teams->sum('max_capacity'));
    }

    public function test_player_join_reconnect_and_team_capacity_enforcement(): void
    {
        $create = $this->postJson('/games/problem-detective/rooms')->json();
        $code = $create['code'];
        $room = DetectiveRoom::with('teams')->where('code', $code)->first();
        $charlieTeam = $room->teams->firstWhere('name', 'Team Charlie'); // max_capacity = 3

        // Join 3 players
        $tokens = [];
        for ($i = 1; $i <= 3; $i++) {
            $join = $this->postJson('/games/problem-detective/join', [
                'code' => $code,
                'name' => 'Detective '.$i,
                'detective_team_id' => $charlieTeam->id,
            ]);
            $join->assertStatus(200)->assertJson(['success' => true]);
            $tokens[] = $join->json('player_token');
        }

        // 4th player rejected because capacity is 3
        $fullJoin = $this->postJson('/games/problem-detective/join', [
            'code' => $code,
            'name' => 'Detective 4',
            'detective_team_id' => $charlieTeam->id,
        ]);
        $fullJoin->assertStatus(422);

        // Reconnect using existing token succeeds without duplicating player
        $reconnect = $this->postJson('/games/problem-detective/join', [
            'code' => $code,
            'name' => 'Detective 1',
            'detective_team_id' => $charlieTeam->id,
            'player_token' => $tokens[0],
        ]);
        $reconnect->assertStatus(200)->assertJson(['reconnected' => true, 'player_token' => $tokens[0]]);
    }

    public function test_secret_case_info_never_leaks_to_player_or_projector_until_reveal(): void
    {
        $create = $this->postJson('/games/problem-detective/rooms')->json();
        $code = $create['code'];
        $hostToken = $create['host_token'];

        $room = DetectiveRoom::with('teams')->where('code', $code)->first();
        $teamA = $room->teams->first();

        $join = $this->postJson('/games/problem-detective/join', [
            'code' => $code,
            'name' => 'Player One',
            'detective_team_id' => $teamA->id,
        ])->json();

        // Advance to investigation
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/phase', ['code' => $code, 'phase' => 'investigation'])
            ->assertStatus(200);

        // Player state must NOT expose hidden_facts or root_cause
        $playerState = $this->withHeaders(['X-Player-Token' => $join['player_token']])
            ->getJson("/games/problem-detective/state?code={$code}&role=player")
            ->assertStatus(200);

        $this->assertArrayNotHasKey('hidden_facts', $playerState->json('room.case'));
        $this->assertArrayNotHasKey('root_cause', $playerState->json('room.case'));

        // Projector state must NOT expose hidden_facts or root_cause
        $projState = $this->getJson("/games/problem-detective/state?code={$code}&role=projector")
            ->assertStatus(200);

        $this->assertArrayNotHasKey('hidden_facts', $projState->json('room.case'));
        $this->assertArrayNotHasKey('root_cause', $projState->json('room.case'));

        // Unauthorized host state attempt must NOT leak secrets
        $fakeHostState = $this->withHeaders(['X-Host-Token' => 'invalid-token'])
            ->getJson("/games/problem-detective/state?code={$code}&role=host")
            ->assertStatus(200);
        $this->assertEquals('projector', $fakeHostState->json('role'));
        $this->assertArrayNotHasKey('root_cause', $fakeHostState->json('room.case'));

        // Unauthorized host action returns 403
        $this->postJson('/games/problem-detective/phase', ['code' => $code, 'phase' => 'reveal'])
            ->assertStatus(403);

        // Once Host switches to reveal, Truth Reveal is visible
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/phase', ['code' => $code, 'phase' => 'reveal'])
            ->assertStatus(200);

        $revealProj = $this->getJson("/games/problem-detective/state?code={$code}&role=projector");
        $this->assertArrayHasKey('root_cause', $revealProj->json('room.case'));
    }

    public function test_full_investigation_lifecycle_atomic_lives_evidence_board_report_and_scoring(): void
    {
        $create = $this->postJson('/games/problem-detective/rooms')->json();
        $code = $create['code'];
        $hostToken = $create['host_token'];
        $room = DetectiveRoom::with('teams')->where('code', $code)->first();
        $teamA = $room->teams[0];
        $teamB = $room->teams[1];

        $pA = $this->postJson('/games/problem-detective/join', [
            'code' => $code, 'name' => 'Alpha 1', 'detective_team_id' => $teamA->id,
        ])->json();

        $pB = $this->postJson('/games/problem-detective/join', [
            'code' => $code, 'name' => 'Bravo 1', 'detective_team_id' => $teamB->id,
        ])->json();

        // Host starts Investigation phase
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/phase', ['code' => $code, 'phase' => 'investigation'])
            ->assertStatus(200);

        // Team A asks normal question (-1 life -> 9 lives)
        $q1 = $this->postJson('/games/problem-detective/questions', [
            'code' => $code,
            'player_token' => $pA['player_token'],
            'question_text' => 'Berapa persentase CPU dan RAM server saat ini?',
        ])->assertStatus(200);
        $this->assertEquals(9, $q1->json('lives'));

        // Team A uses Expert Consultation (-2 lives -> 7 lives)
        $q2 = $this->postJson('/games/problem-detective/questions', [
            'code' => $code,
            'player_token' => $pA['player_token'],
            'question_text' => 'Minta petunjuk khusus area mana yang bottleneck.',
            'is_expert_consultation' => true,
        ])->assertStatus(200);
        $this->assertEquals(7, $q2->json('lives'));

        // Host answers Q1
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/questions/answer', [
                'code' => $code,
                'question_id' => $q1->json('question.id'),
                'status' => 'answered',
                'answer_text' => 'CPU hanya 12% dan RAM 30%.',
            ])->assertStatus(200);

        // Private mode isolation: Team B must NOT see Team A's question
        $stateB = $this->withHeaders(['X-Player-Token' => $pB['player_token']])
            ->getJson("/games/problem-detective/state?code={$code}&role=player")
            ->json();
        $this->assertCount(0, $stateB['room']['questions']);

        // Team A updates Evidence Board
        $this->postJson('/games/problem-detective/board', [
            'code' => $code,
            'player_token' => $pA['player_token'],
            'hypothesis' => 'Masalah ada pada ukuran aset halaman.',
            'eliminated_causes' => ['Server CPU lemah'],
            'evidence_items' => ['CPU 12%, RAM 30%'],
        ])->assertStatus(200);

        // Timer control check
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/timer', ['code' => $code, 'action' => 'pause'])
            ->assertStatus(200);
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/timer', ['code' => $code, 'action' => 'add', 'seconds' => 60])
            ->assertStatus(200);

        // Advance to Final Deduction & submit report
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/phase', ['code' => $code, 'phase' => 'deduction'])
            ->assertStatus(200);

        $this->postJson('/games/problem-detective/report', [
            'code' => $code,
            'player_token' => $pA['player_token'],
            'initial_hypothesis' => 'Server kelebihan beban',
            'evidence_collected' => 'CPU 12%, RAM 30%, banner 28MB',
            'eliminated_possibilities' => 'Kapasitas server dan query DB',
            'root_cause' => 'Gambar banner 28MB tidak dioptimasi',
            'proposed_solution' => 'Kompres banner ke WebP < 250KB dan pasang CDN',
            'validation_plan' => 'Uji DevTools Network dan Lighthouse LCP < 2.5s',
            'confidence_level' => 95,
        ])->assertStatus(200);

        // Host scores Team A
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/score', [
                'code' => $code,
                'team_id' => $teamA->id,
                'question_quality' => 18,
                'root_cause_accuracy' => 30,
                'evidence_strength' => 19,
                'solution_effectiveness' => 18,
                'efficiency' => 7,
                'awards' => ['Master Detective', 'Best Problem Solver'],
            ])->assertStatus(200)->assertJson(['total_score' => 92]);

        // Custom Case Builder CRUD check
        $createdCase = $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson('/games/problem-detective/cases', [
                'code' => $code,
                'title' => 'Custom Case — API Timeout',
                'category' => 'Backend',
                'difficulty' => 'hard',
                'description' => 'Endpoint laporan timeout setelah 30 detik.',
                'initial_assumption' => 'Server down.',
                'mission_objectives' => ['Cari query N+1'],
                'hidden_facts' => ['Ada 5.000 query berulang dalam loop'],
                'evidence' => ['Query log menunjukkan 5.000 SELECT'],
                'root_cause' => 'N+1 query problem pada relasi model',
                'recommended_solutions' => ['Gunakan eager loading with()'],
                'validation_method' => 'Pantau jumlah query turun dari 5.000 menjadi 3',
            ])->assertStatus(200);

        $newCaseId = $createdCase->json('case.id');
        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->postJson("/games/problem-detective/cases/{$newCaseId}/duplicate", ['code' => $code])
            ->assertStatus(200);

        $this->withHeaders(['X-Host-Token' => $hostToken])
            ->deleteJson("/games/problem-detective/cases/{$newCaseId}", ['code' => $code])
            ->assertStatus(200);
    }

    public function test_problem_detective_respects_maintenance_mode(): void
    {
        GameMaintenance::create([
            'game_key' => 'problem-detective',
            'game_name' => 'The Problem Detective',
            'is_maintenance' => true,
            'message' => 'Maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu.',
        ]);

        $this->get('/games/problem-detective')->assertStatus(503)->assertSee('maintanance sama admin');
        $this->get('/problem-detective/play')->assertStatus(503);
    }
}

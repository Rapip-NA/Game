<?php

namespace Tests\Feature;

use App\Models\TwinGame;
use App\Models\TwinPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiapaKembarankuTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_host_screen(): void
    {
        $response = $this->get(route('games.siapa-kembaranku.host'));

        $response->assertStatus(200);
        $response->assertSee('SIAPA KEMBARANKU?');
        $response->assertSee('LOBBY PERMAINAN');
    }

    public function test_can_access_play_screen(): void
    {
        $response = $this->get(route('games.siapa-kembaranku.play'));

        $response->assertStatus(200);
        $response->assertSee('GABUNG KE PERMAINAN');
    }

    public function test_player_can_join_game(): void
    {
        $game = TwinGame::factory()->create(['status' => 'lobby']);

        $response = $this->postJson(route('games.siapa-kembaranku.join'), [
            'code' => $game->code,
            'username' => 'Dimas',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'username' => 'Dimas',
                'game_code' => $game->code,
            ]);

        $this->assertDatabaseHas('twin_players', [
            'twin_game_id' => $game->id,
            'username' => 'Dimas',
        ]);
    }

    public function test_cannot_join_with_duplicate_username(): void
    {
        $game = TwinGame::factory()->create(['status' => 'lobby']);
        TwinPlayer::factory()->create([
            'twin_game_id' => $game->id,
            'username' => 'Nadia',
        ]);

        $response = $this->postJson(route('games.siapa-kembaranku.join'), [
            'code' => $game->code,
            'username' => 'Nadia',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_host_can_start_game_and_logos_are_distributed_with_at_least_two_holders(): void
    {
        $game = TwinGame::factory()->create(['status' => 'lobby']);

        // Create 6 players
        $players = TwinPlayer::factory()->count(6)->create([
            'twin_game_id' => $game->id,
            'secret_logo' => null,
        ]);

        $response = $this->postJson(route('games.siapa-kembaranku.start'), [
            'code' => $game->code,
            'timer_seconds' => 300,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $game->refresh();
        $this->assertSame('discussion', $game->status);

        // Verify every active player has a logo
        $assignedLogos = $game->players()->pluck('secret_logo')->toArray();
        $this->assertCount(6, $assignedLogos);

        // Count frequency of each logo: MUST be >= 2 for every assigned logo!
        $counts = array_count_values($assignedLogos);
        foreach ($counts as $logo => $count) {
            $this->assertGreaterThanOrEqual(2, $count, "Logo {$logo} has only {$count} player(s)!");
        }
    }

    public function test_player_cannot_scan_themselves(): void
    {
        $game = TwinGame::factory()->create(['status' => 'discussion']);
        $player = TwinPlayer::factory()->create([
            'twin_game_id' => $game->id,
            'secret_logo' => '⭐',
        ]);

        $response = $this->postJson(route('games.siapa-kembaranku.scan'), [
            'scanner_token' => $player->player_token,
            'target_token' => $player->player_token,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Eits! Kamu tidak bisa scan QR kamu sendiri!',
            ]);
    }

    public function test_player_can_scan_another_player_and_never_receives_own_logo(): void
    {
        $game = TwinGame::factory()->create(['status' => 'discussion']);
        $scanner = TwinPlayer::factory()->create([
            'twin_game_id' => $game->id,
            'secret_logo' => '❤️',
            'username' => 'Dimas',
        ]);
        $target = TwinPlayer::factory()->create([
            'twin_game_id' => $game->id,
            'secret_logo' => '⭐',
            'username' => 'Nadia',
        ]);

        $response = $this->postJson(route('games.siapa-kembaranku.scan'), [
            'scanner_token' => $scanner->player_token,
            'target_token' => $target->player_token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_username' => 'Nadia',
                'target_logo' => '⭐',
            ]);

        // Security check: Scanner's own logo ('❤️') is NOT in the response
        $this->assertStringNotContainsString('❤️', $response->getContent());
    }

    public function test_cannot_vote_for_self(): void
    {
        $game = TwinGame::factory()->create(['status' => 'voting']);
        $players = TwinPlayer::factory()->count(4)->create([
            'twin_game_id' => $game->id,
        ]);

        $voter = $players[0];

        $response = $this->postJson(route('games.siapa-kembaranku.vote'), [
            'player_token' => $voter->player_token,
            'voted_player_ids' => [$voter->id, $players[1]->id, $players[2]->id],
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Kamu tidak boleh memilih dirimu sendiri!',
            ]);
    }

    public function test_cannot_vote_duplicate_players(): void
    {
        $game = TwinGame::factory()->create(['status' => 'voting']);
        $players = TwinPlayer::factory()->count(4)->create([
            'twin_game_id' => $game->id,
        ]);

        $voter = $players[0];

        $response = $this->postJson(route('games.siapa-kembaranku.vote'), [
            'player_token' => $voter->player_token,
            'voted_player_ids' => [$players[1]->id, $players[1]->id, $players[2]->id],
        ]);

        $response->assertStatus(422);
    }

    public function test_can_submit_valid_votes_and_reveal_results(): void
    {
        $game = TwinGame::factory()->create([
            'status' => 'voting',
            'tie_break_rule' => 'kick_all',
        ]);
        $p1 = TwinPlayer::factory()->create(['twin_game_id' => $game->id, 'secret_logo' => '⭐', 'username' => 'P1']);
        $p2 = TwinPlayer::factory()->create(['twin_game_id' => $game->id, 'secret_logo' => '⭐', 'username' => 'P2']);
        $p3 = TwinPlayer::factory()->create(['twin_game_id' => $game->id, 'secret_logo' => '🔺', 'username' => 'P3']);
        $p4 = TwinPlayer::factory()->create(['twin_game_id' => $game->id, 'secret_logo' => '🔺', 'username' => 'P4']);

        // P1 votes for P2, P3, P4
        $res1 = $this->postJson(route('games.siapa-kembaranku.vote'), [
            'player_token' => $p1->player_token,
            'voted_player_ids' => [$p2->id, $p3->id, $p4->id],
        ]);
        $res1->assertStatus(200);

        // Host triggers reveal
        $revealRes = $this->postJson(route('games.siapa-kembaranku.reveal'), [
            'code' => $game->code,
        ]);

        $revealRes->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $game->refresh();
        $this->assertContains($game->status, ['reveal', 'finished']);
    }
}

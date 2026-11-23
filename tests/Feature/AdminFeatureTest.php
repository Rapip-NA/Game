<?php

namespace Tests\Feature;

use App\Models\GameMaintenance;
use App\Models\GameSuggestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'admin.username' => 'testadmin',
            'admin.password' => 'secret123',
        ]);
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('LOGIN');
        $response->assertSee('ADMIN');
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'wronguser',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('credentials');
        $this->assertFalse(session()->get('is_admin', false));
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'testadmin',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertTrue(session()->get('is_admin'));
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $response = $this->withSession(['is_admin' => true])->get('/admin');

        $response->assertStatus(200);
        $response->assertSeeText('MAINYUK! ADMIN');
    }

    public function test_admin_can_logout(): void
    {
        $response = $this->withSession(['is_admin' => true])->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(session()->get('is_admin', false));
    }

    public function test_user_can_submit_game_suggestion(): void
    {
        $response = $this->post('/game-suggestions', [
            'name' => 'Budi Santoso',
            'game_title' => 'Tebak Gambar Emoji',
            'description' => 'Menebak judul film dari susunan 3 emoji secara cepat',
        ]);

        $response->assertSessionHas('suggestion_success');
        $this->assertDatabaseHas('game_suggestions', [
            'name' => 'Budi Santoso',
            'game_title' => 'Tebak Gambar Emoji',
        ]);
    }

    public function test_admin_can_delete_game_suggestion(): void
    {
        $suggestion = GameSuggestion::create([
            'name' => 'User Test',
            'game_title' => 'Game Test',
            'description' => 'Deskripsi ide game',
        ]);

        $response = $this->withSession(['is_admin' => true])
            ->delete("/admin/suggestions/{$suggestion->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('game_suggestions', [
            'id' => $suggestion->id,
        ]);
    }

    public function test_admin_can_toggle_game_maintenance(): void
    {
        $gameKey = 'siapa-kembaranku';

        $response = $this->withSession(['is_admin' => true])
            ->post("/admin/maintenance/{$gameKey}/toggle");

        $response->assertSessionHas('success');

        $game = GameMaintenance::where('game_key', $gameKey)->first();
        $this->assertNotNull($game);
        $this->assertTrue($game->is_maintenance);

        // When in maintenance, user accessing game gets 503 with maintenance message
        $gameResponse = $this->get('/siapa-kembaranku/host');
        $gameResponse->assertStatus(503);
        $gameResponse->assertSee('maintanance sama admin');

        // Toggle back off
        $this->withSession(['is_admin' => true])
            ->post("/admin/maintenance/{$gameKey}/toggle");

        $game->refresh();
        $this->assertFalse($game->is_maintenance);

        $gameResponseAfter = $this->get('/siapa-kembaranku/host');
        $gameResponseAfter->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\GameMaintenance;
use App\Models\GameSuggestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_suggestion_page_can_be_rendered(): void
    {
        $response = $this->get('/saran');
        $response->assertStatus(200);
        $response->assertSee('KASIH SARAN');

        $aliasResponse = $this->get('/saran-game');
        $aliasResponse->assertRedirect('/saran');
    }

    public function test_user_can_submit_game_suggestion_without_name_and_optional_title(): void
    {
        $response = $this->post('/game-suggestions', [
            'game_title' => 'Tebak Gambar Emoji',
            'description' => '<b>Cara main:</b> Menebak judul film dari <i>3 emoji</i> secara cepat.',
        ]);

        $response->assertRedirect('/saran');
        $response->assertSessionHas('suggestion_success');
        $this->assertDatabaseHas('game_suggestions', [
            'name' => 'Anonim',
            'game_title' => 'Tebak Gambar Emoji',
        ]);
    }

    public function test_user_can_submit_game_suggestion_with_image_upload(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('mockup.png', 300, 300);

        $response = $this->post('/game-suggestions', [
            'description' => 'Ide game seru pakai kartu',
            'image' => $image,
        ]);

        $response->assertRedirect('/saran');
        $response->assertSessionHas('suggestion_success');

        $suggestion = GameSuggestion::latest()->first();
        $this->assertNotNull($suggestion);
        $this->assertEquals('Anonim', $suggestion->name);
        $this->assertEquals('Ide Game Tanpa Judul', $suggestion->game_title);
        $this->assertNotNull($suggestion->image_path);
        $this->assertFileExists(public_path($suggestion->image_path));

        // Cleanup created file
        if (file_exists(public_path($suggestion->image_path))) {
            unlink(public_path($suggestion->image_path));
        }
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

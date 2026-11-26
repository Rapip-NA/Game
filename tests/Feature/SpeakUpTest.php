<?php

namespace Tests\Feature;

use App\Models\GameMaintenance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeakUpTest extends TestCase
{
    use RefreshDatabase;

    public function test_speak_up_page_can_be_rendered_via_game_and_alias_routes(): void
    {
        $response = $this->get('/games/speak-up');
        $response->assertStatus(200);
        $response->assertSee('SPEAK UP!');
        $response->assertSee('Think Fast. Speak Smart.');

        $aliasResponse = $this->get('/speak-up');
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertSee('SPEAK UP!');
    }

    public function test_speak_up_appears_on_landing_page_catalog(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SPEAK UP!');
        $response->assertSee('/games/speak-up');
    }

    public function test_speak_up_respects_maintenance_mode(): void
    {
        GameMaintenance::create([
            'game_key' => 'speak-up',
            'game_name' => 'Speak Up!',
            'is_maintenance' => true,
            'message' => 'Maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu.',
        ]);

        $response = $this->get('/games/speak-up');
        $response->assertStatus(503);
        $response->assertSee('maintanance sama admin');

        $aliasResponse = $this->get('/speak-up');
        $aliasResponse->assertStatus(503);
    }
}

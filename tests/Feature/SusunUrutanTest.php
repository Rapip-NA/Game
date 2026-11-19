<?php

namespace Tests\Feature;

use Tests\TestCase;

class SusunUrutanTest extends TestCase
{
    /**
     * Test that the landing page renders with only Super Password and Susun Urutan cards.
     */
    public function test_landing_page_includes_super_password_and_susun_urutan(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('SUPER PASSWORD');
        $response->assertSee('SUSUN URUTAN');
        $response->assertSee(route('games.super-password'));
        $response->assertSee(route('games.susun-urutan'));

        // Ensure old dummy catalog games are not present in gamesCatalog
        $response->assertDontSee('BALAP KLIK TURBO');
        $response->assertDontSee('SUIT JEPANG PRO');
        $response->assertDontSee('ROKET ANGKASA');
    }

    /**
     * Test that Susun Urutan game page can be accessed.
     */
    public function test_susun_urutan_game_page_is_accessible(): void
    {
        $response = $this->get(route('games.susun-urutan'));

        $response->assertStatus(200);
        $response->assertSee('SUSUN');
        $response->assertSee('URUTAN');
        $response->assertViewHas('themes');
    }

    /**
     * Test that the alias route /susun-urutan is accessible.
     */
    public function test_susun_urutan_alias_route_is_accessible(): void
    {
        $response = $this->get('/susun-urutan');

        $response->assertStatus(200);
        $response->assertSee('SUSUN');
        $response->assertSee('URUTAN');
    }

    /**
     * Test that the player clue route is accessible.
     */
    public function test_susun_urutan_player_route_is_accessible(): void
    {
        $response = $this->get(route('games.susun-urutan.player', ['team' => 1, 'player' => 2]));

        $response->assertStatus(200);
        $response->assertSee('TIM 1');
        $response->assertSee('Pemain Orang ke-2');

        $aliasResponse = $this->get('/tim2/3');
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertSee('TIM 2');
        $aliasResponse->assertSee('Pemain Orang ke-3');
    }

    /**
     * Test that themes API returns json with themes list.
     */
    public function test_susun_urutan_themes_api_returns_valid_data(): void
    {
        $response = $this->getJson(route('games.susun-urutan.themes'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'themes' => [
                '*' => ['theme', 'steps'],
            ],
        ]);
    }
}

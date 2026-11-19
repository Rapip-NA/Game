<?php

namespace Tests\Feature;

use Tests\TestCase;

class SuperPasswordTest extends TestCase
{
    /**
     * Test that the landing page renders with Super Password card.
     */
    public function test_landing_page_includes_super_password_card(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('SUPER PASSWORD');
        $response->assertSee(route('games.super-password'));
    }

    /**
     * Test that Super Password game page can be accessed.
     */
    public function test_super_password_game_page_is_accessible(): void
    {
        $response = $this->get(route('games.super-password'));

        $response->assertStatus(200);
        $response->assertSee('SUPER');
        $response->assertSee('PASSWORD');
        $response->assertViewHas('initialRound');
        $response->assertViewHas('wordBank');
    }

    /**
     * Test that the alias route /super-password also works.
     */
    public function test_super_password_alias_route_is_accessible(): void
    {
        $response = $this->get('/super-password');

        $response->assertStatus(200);
        $response->assertSee('SUPER');
        $response->assertSee('PASSWORD');
    }

    /**
     * Test that the round generation API returns 10 valid items.
     */
    public function test_super_password_round_api_returns_valid_round(): void
    {
        $response = $this->getJson(route('games.super-password.round'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'items' => [
                '*' => ['letter', 'word', 'status'],
            ],
        ]);

        $items = $response->json('items');
        $this->assertCount(10, $items);
    }
}

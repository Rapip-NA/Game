<?php

namespace Database\Factories;

use App\Models\TwinGame;
use App\Models\TwinPlayer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TwinPlayer>
 */
class TwinPlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'twin_game_id' => TwinGame::factory(),
            'username' => fake()->firstName(),
            'player_token' => Str::random(32),
            'secret_logo' => fake()->randomElement(TwinGame::LOGOS),
            'is_kicked' => false,
            'is_winner' => false,
        ];
    }
}

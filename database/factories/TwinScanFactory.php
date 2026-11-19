<?php

namespace Database\Factories;

use App\Models\TwinGame;
use App\Models\TwinPlayer;
use App\Models\TwinScan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TwinScan>
 */
class TwinScanFactory extends Factory
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
            'round_number' => 1,
            'scanner_player_id' => TwinPlayer::factory(),
            'scanned_player_id' => TwinPlayer::factory(),
            'scanned_logo' => fake()->randomElement(TwinGame::LOGOS),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\TwinGame;
use App\Models\TwinPlayer;
use App\Models\TwinVote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TwinVote>
 */
class TwinVoteFactory extends Factory
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
            'voter_player_id' => TwinPlayer::factory(),
            'voted_player_id' => TwinPlayer::factory(),
        ];
    }
}

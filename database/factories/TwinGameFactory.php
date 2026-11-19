<?php

namespace Database\Factories;

use App\Models\TwinGame;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TwinGame>
 */
class TwinGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(6)),
            'status' => 'lobby',
            'current_round' => 1,
            'timer_seconds' => 300,
            'tie_break_rule' => 'kick_all',
        ];
    }
}

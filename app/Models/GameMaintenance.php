<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMaintenance extends Model
{
    use HasFactory;

    /**
     * Complete list of registered games.
     *
     * @var array<string, string>
     */
    public const AVAILABLE_GAMES = [
        'super-password' => 'Super Password',
        'susun-urutan' => 'Susun Urutan',
        'siapa-kembaranku' => 'Siapa Kembaranku?',
        'speaking' => 'Speaking Roulette',
        'speak-up' => 'Speak Up!',
        'problem-detective' => 'The Problem Detective',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'game_key',
        'game_name',
        'is_maintenance',
        'message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_maintenance' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Check if a specific game is under maintenance.
     */
    public static function isUnderMaintenance(string $gameKey): bool
    {
        try {
            return (bool) static::where('game_key', $gameKey)->value('is_maintenance');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Get maintenance message for a game.
     */
    public static function getMaintenanceMessage(string $gameKey): string
    {
        try {
            $message = static::where('game_key', $gameKey)->value('message');
        } catch (\Throwable) {
            $message = null;
        }

        return $message ?: 'Maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu.';
    }

    /**
     * Get all game statuses, auto-populating missing entries.
     *
     * @return array<string, self>
     */
    public static function getStatuses(): array
    {
        try {
            $existing = static::all()->keyBy('game_key');
        } catch (\Throwable) {
            $existing = collect();
        }

        $result = [];

        foreach (self::AVAILABLE_GAMES as $key => $name) {
            $record = $existing->get($key);

            if (! $record) {
                try {
                    $record = static::create([
                        'game_key' => $key,
                        'game_name' => $name,
                        'is_maintenance' => false,
                        'message' => 'Maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu.',
                    ]);
                } catch (\Throwable) {
                    $record = new static([
                        'game_key' => $key,
                        'game_name' => $name,
                        'is_maintenance' => false,
                        'message' => 'Maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu.',
                    ]);
                }
            }

            $result[$key] = $record;
        }

        return $result;
    }
}

<?php

namespace App\Models;

use Database\Factories\TwinGameFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TwinGame extends Model
{
    /** @use HasFactory<TwinGameFactory> */
    use HasFactory;

    public const LOGOS = ['🔺', '⬛', '❤️', '⭐', '🔷', '🌙', '⚡', '🟢'];

    public const LOGO_NAMES = [
        '🔺' => 'Segitiga Merah',
        '⬛' => 'Kotak Hitam',
        '❤️' => 'Hati Cinta',
        '⭐' => 'Bintang Emas',
        '🔷' => 'Berlian Biru',
        '🌙' => 'Bulan Sabit',
        '⚡' => 'Petir Kuning',
        '🟢' => 'Lingkaran Hijau',
    ];

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_round' => 'integer',
            'timer_seconds' => 'integer',
            'timer_ends_at' => 'datetime',
            'eliminated_player_ids' => 'array',
            'last_reveal_data' => 'array',
        ];
    }

    /**
     * @return HasMany<TwinPlayer, $this>
     */
    public function players(): HasMany
    {
        return $this->hasMany(TwinPlayer::class);
    }

    /**
     * @return HasMany<TwinPlayer, $this>
     */
    public function activePlayers(): HasMany
    {
        return $this->hasMany(TwinPlayer::class)->where('is_kicked', false);
    }

    /**
     * @return HasMany<TwinScan, $this>
     */
    public function scans(): HasMany
    {
        return $this->hasMany(TwinScan::class);
    }

    /**
     * @return HasMany<TwinVote, $this>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(TwinVote::class);
    }
}

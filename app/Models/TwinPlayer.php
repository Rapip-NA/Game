<?php

namespace App\Models;

use Database\Factories\TwinPlayerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TwinPlayer extends Model
{
    /** @use HasFactory<TwinPlayerFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * Hidden fields to prevent leaking the secret logo to the player.
     *
     * @var list<string>
     */
    protected $hidden = [
        'secret_logo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_kicked' => 'boolean',
            'is_winner' => 'boolean',
            'kick_round' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<TwinGame, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(TwinGame::class, 'twin_game_id');
    }

    /**
     * @return HasMany<TwinScan, $this>
     */
    public function scansMade(): HasMany
    {
        return $this->hasMany(TwinScan::class, 'scanner_player_id');
    }

    /**
     * @return HasMany<TwinScan, $this>
     */
    public function scansReceived(): HasMany
    {
        return $this->hasMany(TwinScan::class, 'scanned_player_id');
    }

    /**
     * @return HasMany<TwinVote, $this>
     */
    public function votesCast(): HasMany
    {
        return $this->hasMany(TwinVote::class, 'voter_player_id');
    }

    /**
     * @return HasMany<TwinVote, $this>
     */
    public function votesReceived(): HasMany
    {
        return $this->hasMany(TwinVote::class, 'voted_player_id');
    }
}

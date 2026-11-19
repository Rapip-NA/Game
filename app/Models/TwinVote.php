<?php

namespace App\Models;

use Database\Factories\TwinVoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwinVote extends Model
{
    /** @use HasFactory<TwinVoteFactory> */
    use HasFactory;

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
            'round_number' => 'integer',
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
     * @return BelongsTo<TwinPlayer, $this>
     */
    public function voter(): BelongsTo
    {
        return $this->belongsTo(TwinPlayer::class, 'voter_player_id');
    }

    /**
     * @return BelongsTo<TwinPlayer, $this>
     */
    public function voted(): BelongsTo
    {
        return $this->belongsTo(TwinPlayer::class, 'voted_player_id');
    }
}

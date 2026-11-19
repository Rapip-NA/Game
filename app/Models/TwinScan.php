<?php

namespace App\Models;

use Database\Factories\TwinScanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwinScan extends Model
{
    /** @use HasFactory<TwinScanFactory> */
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
    public function scanner(): BelongsTo
    {
        return $this->belongsTo(TwinPlayer::class, 'scanner_player_id');
    }

    /**
     * @return BelongsTo<TwinPlayer, $this>
     */
    public function scanned(): BelongsTo
    {
        return $this->belongsTo(TwinPlayer::class, 'scanned_player_id');
    }
}

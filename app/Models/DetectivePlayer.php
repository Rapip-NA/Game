<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetectivePlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'detective_room_id',
        'detective_team_id',
        'name',
        'player_token',
        'is_ready',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'is_ready' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(DetectiveRoom::class, 'detective_room_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(DetectiveTeam::class, 'detective_team_id');
    }
}

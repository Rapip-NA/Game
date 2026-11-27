<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetectiveQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'detective_room_id',
        'detective_team_id',
        'detective_player_id',
        'question_text',
        'is_expert_consultation',
        'status',
        'answer_text',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'is_expert_consultation' => 'boolean',
            'answered_at' => 'datetime',
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

    public function player(): BelongsTo
    {
        return $this->belongsTo(DetectivePlayer::class, 'detective_player_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetectiveTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'detective_room_id',
        'name',
        'max_capacity',
        'sort_order',
        'lives',
        'questions_asked_count',
        'notes',
        'hypothesis',
        'eliminated_causes',
        'evidence_items',
        'final_report',
        'submitted_at',
        'scores',
        'total_score',
        'awards',
    ];

    protected function casts(): array
    {
        return [
            'max_capacity' => 'integer',
            'sort_order' => 'integer',
            'lives' => 'integer',
            'questions_asked_count' => 'integer',
            'eliminated_causes' => 'array',
            'evidence_items' => 'array',
            'final_report' => 'array',
            'submitted_at' => 'datetime',
            'scores' => 'array',
            'total_score' => 'integer',
            'awards' => 'array',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(DetectiveRoom::class, 'detective_room_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(DetectivePlayer::class, 'detective_team_id')->orderBy('id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DetectiveQuestion::class, 'detective_team_id')->orderBy('id');
    }
}

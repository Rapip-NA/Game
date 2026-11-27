<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetectiveRoom extends Model
{
    use HasFactory;

    public const PHASES = [
        'lobby',
        'briefing',
        'discussion',
        'investigation',
        'deduction',
        'defense',
        'reveal',
        'leaderboard',
    ];

    public const DEFAULT_PHASE_DURATIONS = [
        'briefing' => 120,
        'discussion' => 180,
        'investigation' => 600,
        'deduction' => 300,
    ];

    protected $fillable = [
        'code',
        'host_token',
        'detective_case_id',
        'phase',
        'investigation_mode',
        'target_players',
        'default_lives',
        'phase_durations',
        'phase_ends_at',
        'phase_paused_remaining',
        'defense_team_id',
        'breaking_news_feed',
    ];

    protected function casts(): array
    {
        return [
            'target_players' => 'integer',
            'default_lives' => 'integer',
            'phase_durations' => 'array',
            'phase_ends_at' => 'datetime',
            'phase_paused_remaining' => 'integer',
            'defense_team_id' => 'integer',
            'breaking_news_feed' => 'array',
        ];
    }

    public function detectiveCase(): BelongsTo
    {
        return $this->belongsTo(DetectiveCase::class, 'detective_case_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(DetectiveTeam::class, 'detective_room_id')->orderBy('sort_order');
    }

    public function players(): HasMany
    {
        return $this->hasMany(DetectivePlayer::class, 'detective_room_id')->orderBy('id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DetectiveQuestion::class, 'detective_room_id')->orderBy('id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetectiveCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'difficulty',
        'initial_assumption',
        'mission_objectives',
        'hidden_facts',
        'evidence',
        'false_leads',
        'special_hints',
        'breaking_news',
        'root_cause',
        'recommended_solutions',
        'validation_method',
        'default_duration',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'mission_objectives' => 'array',
            'hidden_facts' => 'array',
            'evidence' => 'array',
            'false_leads' => 'array',
            'special_hints' => 'array',
            'breaking_news' => 'array',
            'recommended_solutions' => 'array',
            'default_duration' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Return strictly public case briefing fields safe for players and projector.
     *
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'initial_assumption' => $this->initial_assumption,
            'mission_objectives' => $this->mission_objectives ?? [],
            'default_duration' => $this->default_duration,
        ];
    }

    /**
     * Return full truth reveal data (only safe when phase is reveal or leaderboard, or for Host).
     *
     * @return array<string, mixed>
     */
    public function toTruthRevealArray(): array
    {
        return [
            ...$this->toPublicArray(),
            'hidden_facts' => $this->hidden_facts ?? [],
            'evidence' => $this->evidence ?? [],
            'false_leads' => $this->false_leads ?? [],
            'root_cause' => $this->root_cause,
            'recommended_solutions' => $this->recommended_solutions ?? [],
            'validation_method' => $this->validation_method,
        ];
    }
}

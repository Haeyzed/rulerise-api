<?php

namespace App\Models;

use App\Enums\ProficiencyLevelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateSkill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'skill_id',
        'proficiency_level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'proficiency_level' => ProficiencyLevelEnum::class,
    ];

    /**
     * Get the candidate that owns the skill.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    /**
     * Get the skill that owns the candidate skill.
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Get the proficiency level label attribute.
     */
    public function getProficiencyLevelLabelAttribute(): ?string
    {
        return $this->proficiency_level ? $this->proficiency_level->label() : null;
    }

    /**
     * Get the proficiency level color attribute.
     */
    public function getProficiencyLevelColorAttribute(): ?string
    {
        return $this->proficiency_level ? $this->proficiency_level->color() : null;
    }
}


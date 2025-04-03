<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'icon',
        'color',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the jobs for the skill.
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_skills')
            ->withTimestamps();
    }

    /**
     * Get the candidate skills for the skill.
     */
    public function candidateSkills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class);
    }

    /**
     * Get the job pool skills for the skill.
     */
    public function jobPoolSkills(): HasMany
    {
        return $this->hasMany(JobPoolSkill::class);
    }

    /**
     * Get the candidates for the skill.
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(CandidateProfile::class, 'candidate_skills', 'skill_id', 'candidate_id')
            ->withPivot('proficiency_level')
            ->withTimestamps();
    }

    /**
     * Get the job pools for the skill.
     */
    public function jobPools(): BelongsToMany
    {
        return $this->belongsToMany(JobPool::class, 'job_pool_skills', 'skill_id', 'job_pool_id')
            ->withPivot('importance')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active skills.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
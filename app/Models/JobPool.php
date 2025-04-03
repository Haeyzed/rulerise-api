<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class JobPool extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'is_active',
        'is_public',
        'start_date',
        'end_date',
        'target_hiring_count',
        'current_hiring_count',
        'department',
        'location',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'target_hiring_count' => 'integer',
        'current_hiring_count' => 'integer',
    ];

    /**
     * Get the company that owns the job pool.
     *
     * @return BelongsTo<Company, JobPool>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the jobs for the job pool.
     *
     * @return BelongsToMany<Job>
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_pool_jobs')
            ->withTimestamps();
    }

    /**
     * Get the skills for the job pool.
     *
     * @return BelongsToMany<Skill>
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_pool_skills')
            ->withPivot('importance')
            ->withTimestamps();
    }

    /**
     * Get the job pool skills for the job pool.
     *
     * @return HasMany<JobPoolSkill>
     */
    public function jobPoolSkills(): HasMany
    {
        return $this->hasMany(JobPoolSkill::class);
    }

    /**
     * Get the candidates for the job pool.
     *
     * @return BelongsToMany<CandidateProfile>
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(CandidateProfile::class, 'candidate_job_pools', 'job_pool_id', 'candidate_id')
            ->withPivot('status', 'notes', 'added_by_user_id')
            ->withTimestamps();
    }

    /**
     * Get the candidate job pools for the job pool.
     *
     * @return HasMany<CandidateJobPool>
     */
    public function candidateJobPools(): HasMany
    {
        return $this->hasMany(CandidateJobPool::class);
    }

    /**
     * Get the status history for the job pool candidates.
     *
     * @return HasManyThrough<JobPoolStatusHistory, CandidateJobPool>
     */
    public function candidateStatusHistory(): HasManyThrough
    {
        return $this->hasManyThrough(
            JobPoolStatusHistory::class,
            CandidateJobPool::class,
            'job_pool_id',
            'candidate_job_pool_id'
        );
    }

    /**
     * Scope a query to only include active job pools.
     *
     * @param Builder<JobPool> $query
     * @return Builder<JobPool>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include public job pools.
     *
     * @param Builder<JobPool> $query
     * @return Builder<JobPool>
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include job pools with active dates.
     *
     * @param Builder<JobPool> $query
     * @return Builder<JobPool>
     */
    public function scopeActiveDates(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', now());
        });
    }

    /**
     * Increment the current hiring count.
     *
     * @return void
     */
    public function incrementHiringCount(): void
    {
        $this->increment('current_hiring_count');
    }

    /**
     * Get the is active dates attribute.
     *
     * @return bool
     */
    public function getIsActiveDatesAttribute(): bool
    {
        return !$this->end_date || $this->end_date->isFuture();
    }

    /**
     * Get the progress percentage attribute.
     *
     * @return int|null
     */
    public function getProgressPercentageAttribute(): ?int
    {
        if (!$this->target_hiring_count) {
            return null;
        }

        return min(100, (int) round(($this->current_hiring_count / $this->target_hiring_count) * 100));
    }
}


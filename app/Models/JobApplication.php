<?php

namespace App\Models;

use App\Enums\ApplicationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'candidate_id',
        'resume_id',
        'cover_letter',
        'status',
        'expected_salary',
        'salary_currency',
        'availability_date',
        'notes',
        'is_viewed',
        'viewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_viewed' => 'boolean',
        'viewed_at' => 'datetime',
        'availability_date' => 'date',
        'expected_salary' => 'decimal:2',
        'status' => ApplicationStatusEnum::class,
    ];

    /**
     * Get the job that owns the application.
     *
     * @return BelongsTo<Job, JobApplication>
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the candidate that owns the application.
     *
     * @return BelongsTo<CandidateProfile, JobApplication>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    /**
     * Get the resume that owns the application.
     *
     * @return BelongsTo<CandidateResume, JobApplication>
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(CandidateResume::class, 'resume_id');
    }

    /**
     * Get the status history for the application.
     *
     * @return HasMany<JobApplicationStatusHistory>
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(JobApplicationStatusHistory::class, 'application_id');
    }

    /**
     * Get the interviews for the application.
     *
     * @return HasMany<Interview>
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'application_id');
    }

    /**
     * Get the messages for the application.
     *
     * @return HasMany<Message>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'application_id');
    }

    /**
     * Mark the application as viewed.
     *
     * @return void
     */
    public function markAsViewed(): void
    {
        if (!$this->is_viewed) {
            $this->update([
                'is_viewed' => true,
                'viewed_at' => now(),
            ]);
        }
    }

    /**
     * Update the application status.
     *
     * @param string $status
     * @param string|null $notes
     * @param int|null $changedByUserId
     * @return self
     */
    public function updateStatus(string $status, ?string $notes = null, ?int $changedByUserId = null): self
    {
        $this->update(['status' => $status]);

        // Create status history record
        $this->statusHistory()->create([
            'status' => $status,
            'notes' => $notes,
            'changed_by_user_id' => $changedByUserId,
        ]);

        return $this;
    }

    /**
     * Scope a query to only include applications with a specific status.
     *
     * @param Builder<JobApplication> $query
     * @param string|ApplicationStatusEnum $status
     * @return Builder<JobApplication>
     */
    public function scopeWithStatus(Builder $query, ApplicationStatusEnum|string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include viewed applications.
     *
     * @param Builder<JobApplication> $query
     * @return Builder<JobApplication>
     */
    public function scopeViewed(Builder $query): Builder
    {
        return $query->where('is_viewed', true);
    }

    /**
     * Scope a query to only include unviewed applications.
     *
     * @param Builder<JobApplication> $query
     * @return Builder<JobApplication>
     */
    public function scopeUnviewed(Builder $query): Builder
    {
        return $query->where('is_viewed', false);
    }
}


<?php

namespace App\Models;

use App\Enums\ApplicationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CandidateJobPool extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'job_pool_id',
        'status',
        'notes',
        'added_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ApplicationStatusEnum::class,
    ];

    /**
     * Get the candidate that owns the job pool.
     *
     * @return BelongsTo<CandidateProfile, CandidateJobPool>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    /**
     * Get the job pool that owns the candidate.
     *
     * @return BelongsTo<JobPool, CandidateJobPool>
     */
    public function jobPool(): BelongsTo
    {
        return $this->belongsTo(JobPool::class);
    }

    /**
     * Get the user that added the candidate to the job pool.
     *
     * @return BelongsTo<User, CandidateJobPool>
     */
    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    /**
     * Get the status history for the candidate job pool.
     *
     * @return HasMany<JobPoolStatusHistory>
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(JobPoolStatusHistory::class, 'candidate_job_pool_id');
    }

    /**
     * Get the status label attribute.
     *
     * @return string|null
     */
    public function getStatusLabelAttribute(): ?string
    {
        return $this->status ? $this->status->label() : null;
    }

    /**
     * Get the status color attribute.
     *
     * @return string|null
     */
    public function getStatusColorAttribute(): ?string
    {
        return $this->status ? $this->status->color() : null;
    }

    /**
     * Update the status.
     *
     * @param string $status
     * @param string|null $notes
     * @param int|null $changedByUserId
     * @return bool
     */
    public function updateStatus(string $status, ?string $notes = null, ?int $changedByUserId = null): bool
    {
        $result = $this->update(['status' => $status]);

        if ($result) {
            $this->statusHistory()->create([
                'status' => $status,
                'notes' => $notes,
                'changed_by_user_id' => $changedByUserId,
            ]);
        }

        return $result;
    }

    /**
     * Scope a query to only include candidates with a specific status.
     *
     * @param Builder<CandidateJobPool> $query
     * @param string|ApplicationStatusEnum $status
     * @return Builder<CandidateJobPool>
     */
    public function scopeWithStatus(Builder $query, $status): Builder
    {
        return $query->where('status', $status);
    }
}


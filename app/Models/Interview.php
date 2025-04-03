<?php

namespace App\Models;

use App\Enums\InterviewStatusEnum;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Interview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'application_id',
        'scheduled_by',
        'interview_date',
        'duration_minutes',
        'location',
        'meeting_link',
        'is_online',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'interview_date' => 'datetime',
        'duration_minutes' => 'integer',
        'is_online' => 'boolean',
        'status' => InterviewStatusEnum::class,
    ];

    /**
     * Get the application that owns the interview.
     *
     * @return BelongsTo<JobApplication, Interview>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'application_id');
    }

    /**
     * Get the user that scheduled the interview.
     *
     * @return BelongsTo<User, Interview>
     */
    public function scheduledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
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
     * Get the formatted interview date attribute.
     *
     * @return string
     */
    public function getFormattedInterviewDateAttribute(): string
    {
        return $this->interview_date->format('M d, Y \a\t h:i A');
    }

    /**
     * Get the formatted duration attribute.
     *
     * @return string
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return $hours . ' ' . ($hours === 1 ? 'hour' : 'hours') . ' ' . $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
        } elseif ($hours > 0) {
            return $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
        } else {
            return $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
        }
    }

    /**
     * Get the is upcoming attribute.
     *
     * @return bool
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->interview_date->isFuture() && $this->status === InterviewStatusEnum::SCHEDULED;
    }

    /**
     * Get the is past attribute.
     *
     * @return bool
     */
    public function getIsPastAttribute(): bool
    {
        return $this->interview_date->isPast();
    }

    /**
     * Scope a query to only include upcoming interviews.
     *
     * @param Builder<Interview> $query
     * @return Builder<Interview>
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('interview_date', '>', now())
            ->where('status', InterviewStatusEnum::SCHEDULED->value);
    }

    /**
     * Scope a query to only include past interviews.
     *
     * @param Builder<Interview> $query
     * @return Builder<Interview>
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('interview_date', '<', now());
    }

    /**
     * Scope a query to only include interviews with a specific status.
     *
     * @param Builder<Interview> $query
     * @param string|InterviewStatusEnum $status
     * @return Builder<Interview>
     */
    public function scopeWithStatus(Builder $query, InterviewStatusEnum|string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by date range.
     *
     * @param Builder<Interview> $query
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return Builder<Interview>
     */
    public function scopeDateRange(Builder $query, DateTime $startDate, DateTime $endDate): Builder
    {
        return $query->whereBetween('interview_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by application.
     *
     * @param Builder<Interview> $query
     * @param int $applicationId
     * @return Builder<Interview>
     */
    public function scopeForApplication(Builder $query, int $applicationId): Builder
    {
        return $query->where('application_id', $applicationId);
    }
}


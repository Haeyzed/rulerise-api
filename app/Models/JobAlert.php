<?php

namespace App\Models;

use App\Enums\AlertFrequencyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class JobAlert extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'keywords',
        'category_id',
        'job_type_id',
        'experience_level_id',
        'location',
        'country_id',
        'is_remote',
        'min_salary',
        'frequency',
        'is_active',
        'last_sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_remote' => 'boolean',
        'min_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
        'frequency' => AlertFrequencyEnum::class,
    ];

    /**
     * Get the user that owns the job alert.
     *
     * @return BelongsTo<User, JobAlert>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the job alert.
     *
     * @return BelongsTo<JobCategory, JobAlert>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Get the job type that owns the job alert.
     *
     * @return BelongsTo<JobType, JobAlert>
     */
    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    /**
     * Get the experience level that owns the job alert.
     *
     * @return BelongsTo<ExperienceLevel, JobAlert>
     */
    public function experienceLevel(): BelongsTo
    {
        return $this->belongsTo(ExperienceLevel::class);
    }

    /**
     * Get the country that owns the job alert.
     *
     * @return BelongsTo<Country, JobAlert>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the frequency label attribute.
     *
     * @return string|null
     */
    public function getFrequencyLabelAttribute(): ?string
    {
        return $this->frequency ? $this->frequency->label() : null;
    }

    /**
     * Check if the alert is due to be sent.
     *
     * @return bool
     */
    public function isDue(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->last_sent_at) {
            return true;
        }

        return match ($this->frequency) {
            AlertFrequencyEnum::DAILY => $this->last_sent_at->diffInDays(now()) >= 1,
            AlertFrequencyEnum::WEEKLY => $this->last_sent_at->diffInDays(now()) >= 7,
            AlertFrequencyEnum::BIWEEKLY => $this->last_sent_at->diffInDays(now()) >= 14,
            AlertFrequencyEnum::MONTHLY => $this->last_sent_at->diffInDays(now()) >= 30,
            default => false,
        };
    }

    /**
     * Scope a query to only include active alerts.
     *
     * @param Builder<JobAlert> $query
     * @return Builder<JobAlert>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include due alerts.
     *
     * @param Builder<JobAlert> $query
     * @return Builder<JobAlert>
     */
    public function scopeDue(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('last_sent_at')
                    ->orWhere(function ($sq) {
                        $sq->where('frequency', AlertFrequencyEnum::DAILY->value)
                            ->where('last_sent_at', '<', now()->subDay());
                    })
                    ->orWhere(function ($sq) {
                        $sq->where('frequency', AlertFrequencyEnum::WEEKLY->value)
                            ->where('last_sent_at', '<', now()->subWeek());
                    })
                    ->orWhere(function ($sq) {
                        $sq->where('frequency', AlertFrequencyEnum::BIWEEKLY->value)
                            ->where('last_sent_at', '<', now()->subWeeks(2));
                    })
                    ->orWhere(function ($sq) {
                        $sq->where('frequency', AlertFrequencyEnum::MONTHLY->value)
                            ->where('last_sent_at', '<', now()->subMonth());
                    });
            });
    }
}

<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'plan_id',
        'starts_at',
        'expires_at',
        'is_active',
        'payment_status',
        'payment_method',
        'transaction_id',
        'job_posts_used',
        'featured_jobs_used',
        'resume_views_used',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'payment_status' => PaymentStatusEnum::class,
        'job_posts_used' => 'integer',
        'featured_jobs_used' => 'integer',
        'resume_views_used' => 'integer',
    ];

    /**
     * Get the company that owns the subscription.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the plan that owns the subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Check if the subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get the remaining days of the subscription.
     */
    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->expires_at || $this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }

    /**
     * Get the job posts remaining attribute.
     */
    public function getJobPostsRemainingAttribute(): ?int
    {
        if ($this->plan->job_posts_limit === null) {
            return null; // Unlimited
        }

        return max(0, $this->plan->job_posts_limit - $this->job_posts_used);
    }

    /**
     * Get the featured jobs remaining attribute.
     */
    public function getFeaturedJobsRemainingAttribute(): ?int
    {
        if ($this->plan->featured_jobs_limit === null) {
            return null; // Unlimited
        }

        return max(0, $this->plan->featured_jobs_limit - $this->featured_jobs_used);
    }

    /**
     * Get the resume views remaining attribute.
     */
    public function getResumeViewsRemainingAttribute(): ?int
    {
        if ($this->plan->resume_views_limit === null) {
            return null; // Unlimited
        }

        return max(0, $this->plan->resume_views_limit - $this->resume_views_used);
    }

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope a query to only include expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}


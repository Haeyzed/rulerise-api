<?php

namespace App\Models;

use App\Enums\SupportLevelEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
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
        'description',
        'price',
        'currency',
        'duration_days',
        'job_posts_limit',
        'featured_jobs_limit',
        'resume_views_limit',
        'job_alerts',
        'candidate_search',
        'resume_access',
        'company_profile',
        'support_level',
        'is_active',
        'is_featured',
        'features',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'job_posts_limit' => 'integer',
        'featured_jobs_limit' => 'integer',
        'resume_views_limit' => 'integer',
        'job_alerts' => 'boolean',
        'candidate_search' => 'boolean',
        'resume_access' => 'boolean',
        'company_profile' => 'boolean',
        'support_level' => SupportLevelEnum::class,
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'features' => 'array',
    ];

    /**
     * Get the company subscriptions for the subscription plan.
     */
    public function companySubscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class, 'plan_id');
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    /**
     * Get the formatted duration attribute.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_days % 30 === 0) {
            $months = $this->duration_days / 30;
            return $months . ' ' . ($months === 1 ? 'month' : 'months');
        } elseif ($this->duration_days % 7 === 0) {
            $weeks = $this->duration_days / 7;
            return $weeks . ' ' . ($weeks === 1 ? 'week' : 'weeks');
        } else {
            return $this->duration_days . ' ' . ($this->duration_days === 1 ? 'day' : 'days');
        }
    }

    /**
     * Get the support level label attribute.
     */
    public function getSupportLevelLabelAttribute(): string
    {
        return $this->support_level->label();
    }

    /**
     * Get the support level color attribute.
     */
    public function getSupportLevelColorAttribute(): string
    {
        return $this->support_level->color();
    }

    /**
     * Get the support level description attribute.
     */
    public function getSupportLevelDescriptionAttribute(): string
    {
        return $this->support_level->description();
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured plans.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
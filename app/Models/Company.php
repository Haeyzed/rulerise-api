<?php

namespace App\Models;

use App\Services\Storage\StorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'slug',
        'logo',
        'phone',
        'banner',
        'website',
        'description',
        'industry',
        'company_size_id',
        'founded_year',
        'address',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'is_active',
        'is_verified',
        'is_featured',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'instagram_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'founded_year' => 'integer',
    ];

    /**
     * Get the user that owns the company.
     *
     * @return BelongsTo<User, Company>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company size that owns the company.
     *
     * @return BelongsTo<CompanySize, Company>
     */
    public function companySize(): BelongsTo
    {
        return $this->belongsTo(CompanySize::class);
    }

    /**
     * Get the country that owns the company.
     *
     * @return BelongsTo<Country, Company>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that owns the company.
     *
     * @return BelongsTo<State, Company>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that owns the company.
     *
     * @return BelongsTo<City, Company>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the jobs for the company.
     *
     * @return HasMany<Job>
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get the subscriptions for the company.
     *
     * @return HasMany<CompanySubscription>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    /**
     * Get the reviews for the company.
     *
     * @return HasMany<CompanyReview>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class);
    }

    /**
     * Get the job pools for the company.
     *
     * @return HasMany<JobPool>
     */
    public function jobPools(): HasMany
    {
        return $this->hasMany(JobPool::class);
    }

    /**
     * Get the candidate bookmarks for the company.
     *
     * @return HasMany<CandidateBookmark>
     */
    public function candidateBookmarks(): HasMany
    {
        return $this->hasMany(CandidateBookmark::class);
    }

    /**
     * Get the logo URL attribute.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return app(StorageService::class)->url($this->logo);
    }

    /**
     * Get the banner URL attribute.
     *
     * @return string|null
     */
    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner) {
            return null;
        }

        return app(StorageService::class)->url($this->banner);
    }

    /**
     * Get the full location attribute.
     *
     * @return string
     */
    public function getFullLocationAttribute(): string
    {
        $parts = [];

        if ($this->city) {
            $parts[] = $this->city->name;
        }

        if ($this->state) {
            $parts[] = $this->state->name;
        }

        if ($this->country) {
            $parts[] = $this->country->name;
        }

        return implode(', ', $parts);
    }

    /**
     * Get the active subscription for the company.
     *
     * @return CompanySubscription|null
     */
    public function activeSubscription(): ?CompanySubscription
    {
        return $this->subscriptions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->latest('starts_at')
            ->first();
    }

    /**
     * Scope a query to only include verified companies.
     *
     * @param Builder<Company> $query
     * @return Builder<Company>
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include featured companies.
     *
     * @param Builder<Company> $query
     * @return Builder<Company>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}


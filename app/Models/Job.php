<?php

namespace App\Models;

use App\Enums\SalaryPeriodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_listings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'category_id',
        'job_type_id',
        'experience_level_id',
        'education_level_id',
        'title',
        'slug',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'min_salary',
        'max_salary',
        'salary_currency',
        'salary_period',
        'is_salary_visible',
        'location',
        'address',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'is_remote',
        'application_deadline',
        'is_active',
        'is_featured',
        'is_urgent',
        'vacancies',
        'views_count',
        'applications_count',
        'external_apply_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_salary_visible' => 'boolean',
        'is_remote' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'application_deadline' => 'date',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'views_count' => 'integer',
        'applications_count' => 'integer',
        'vacancies' => 'integer',
        'salary_period' => SalaryPeriodEnum::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_salary_range',
        'is_deadline_passed',
        'days_until_deadline',
        'is_recently_posted',
    ];

    /**
     * Get the company that owns the job.
     *
     * @return BelongsTo<Company, Job>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category that owns the job.
     *
     * @return BelongsTo<JobCategory, Job>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    /**
     * Get the job type that owns the job.
     *
     * @return BelongsTo<JobType, Job>
     */
    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    /**
     * Get the experience level that owns the job.
     *
     * @return BelongsTo<ExperienceLevel, Job>
     */
    public function experienceLevel(): BelongsTo
    {
        return $this->belongsTo(ExperienceLevel::class);
    }

    /**
     * Get the education level that owns the job.
     *
     * @return BelongsTo<EducationLevel, Job>
     */
    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     * Get the country that owns the job.
     *
     * @return BelongsTo<Country, Job>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that owns the job.
     *
     * @return BelongsTo<State, Job>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that owns the job.
     *
     * @return BelongsTo<City, Job>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the skills for the job.
     *
     * @return BelongsToMany<Skill>
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills')
            ->withTimestamps();
    }

    /**
     * Get the applications for the job.
     *
     * @return HasMany<JobApplication>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the bookmarks for the job.
     *
     * @return HasMany<JobBookmark>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(JobBookmark::class);
    }

    /**
     * Get the job pools for the job.
     *
     * @return BelongsToMany<JobPool>
     */
    public function jobPools(): BelongsToMany
    {
        return $this->belongsToMany(JobPool::class, 'job_pool_jobs')
            ->withTimestamps();
    }

    /**
     * Get the formatted salary range attribute.
     *
     * @return string|null
     */
    public function getFormattedSalaryRangeAttribute(): ?string
    {
        if (!$this->min_salary && !$this->max_salary) {
            return null;
        }

        $currency = $this->salary_currency ?? 'USD';
        $period = $this->salary_period ? $this->salary_period->label() : 'yearly';

        if ($this->min_salary && $this->max_salary) {
            return "{$currency} {$this->min_salary} - {$this->max_salary} {$period}";
        } elseif ($this->min_salary) {
            return "{$currency} {$this->min_salary}+ {$period}";
        } else {
            return "Up to {$currency} {$this->max_salary} {$period}";
        }
    }

    /**
     * Get the full location attribute.
     *
     * @return string
     */
    public function getFullLocationAttribute(): string
    {
        if ($this->is_remote) {
            return 'Remote';
        }

        $parts = [];

        if ($this->location) {
            $parts[] = $this->location;
        } else {
            if ($this->city) {
                $parts[] = $this->city->name;
            }

            if ($this->state) {
                $parts[] = $this->state->name;
            }

            if ($this->country) {
                $parts[] = $this->country->name;
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Get the is deadline passed attribute.
     *
     * @return bool
     */
    public function getIsDeadlinePassedAttribute(): bool
    {
        if (!$this->application_deadline) {
            return false;
        }

        return $this->application_deadline->isPast();
    }

    /**
     * Get the days until deadline attribute.
     *
     * @return int|null
     */
    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->application_deadline || $this->application_deadline->isPast()) {
            return null;
        }

        return now()->diffInDays($this->application_deadline);
    }

    /**
     * Get the is recently posted attribute.
     *
     * @return bool
     */
    public function getIsRecentlyPostedAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    /**
     * Scope a query to only include active jobs.
     *
     * @param Builder<Job> $query
     * @return Builder<Job>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured jobs.
     *
     * @param Builder<Job> $query
     * @return Builder<Job>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include urgent jobs.
     *
     * @param Builder<Job> $query
     * @return Builder<Job>
     */
    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope a query to only include remote jobs.
     *
     * @param Builder<Job> $query
     * @return Builder<Job>
     */
    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('is_remote', true);
    }

    /**
     * Scope a query to only include jobs with active applications.
     *
     * @param Builder<Job> $query
     * @return Builder<Job>
     */
    public function scopeActiveApplications(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('application_deadline')
                ->orWhere('application_deadline', '>=', now());
        });
    }

    /**
     * Scope a query to filter jobs by location.
     *
     * @param Builder<Job> $query
     * @param string $location
     * @return Builder<Job>
     */
    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where(function ($q) use ($location) {
            $q->where('location', 'like', "%{$location}%")
                ->orWhereHas('city', function ($sq) use ($location) {
                    $sq->where('name', 'like', "%{$location}%");
                })
                ->orWhereHas('state', function ($sq) use ($location) {
                    $sq->where('name', 'like', "%{$location}%");
                })
                ->orWhereHas('country', function ($sq) use ($location) {
                    $sq->where('name', 'like', "%{$location}%");
                });
        });
    }

    /**
     * Scope a query to filter jobs by country.
     *
     * @param Builder<Job> $query
     * @param int $countryId
     * @return Builder<Job>
     */
    public function scopeInCountry(Builder $query, $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope a query to filter jobs by state.
     *
     * @param Builder<Job> $query
     * @param int $stateId
     * @return Builder<Job>
     */
    public function scopeInState(Builder $query, $stateId): Builder
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope a query to filter jobs by city.
     *
     * @param Builder<Job> $query
     * @param int $cityId
     * @return Builder<Job>
     */
    public function scopeInCity(Builder $query, $cityId): Builder
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope a query to filter jobs by salary range.
     *
     * @param Builder<Job> $query
     * @param float $min
     * @param float|null $max
     * @return Builder<Job>
     */
    public function scopeSalaryRange(Builder $query, float $min, ?float $max = null): Builder
    {
        return $query->where(function ($q) use ($min, $max) {
            $q->where(function ($sq) use ($min) {
                $sq->where('min_salary', '>=', $min);
            })->orWhere(function ($sq) use ($min, $max) {
                $sq->where('min_salary', '<=', $min)
                    ->where('max_salary', '>=', $min);
            });

            if ($max) {
                $q->where(function ($sq) use ($max) {
                    $sq->where('max_salary', '<=', $max)
                        ->orWhereNull('max_salary');
                });
            }
        });
    }

    /**
     * Scope a query to filter jobs by posting date.
     *
     * @param Builder<Job> $query
     * @param int $days
     * @return Builder<Job>
     */
    public function scopePostedWithin(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to filter jobs by expiring soon.
     *
     * @param Builder<Job> $query
     * @param int $days
     * @return Builder<Job>
     */
    public function scopeExpiringSoon(Builder $query, int $days = 7): Builder
    {
        return $query->where('application_deadline', '<=', now()->addDays($days))
            ->where('application_deadline', '>', now());
    }

    /**
     * Scope a query to filter jobs by company verification status.
     *
     * @param Builder<Job> $query
     * @return Builder<Job>
     */
    public function scopeVerifiedCompany(Builder $query): Builder
    {
        return $query->whereHas('company', function ($q) {
            $q->where('is_verified', true);
        });
    }

    /**
     * Scope a query to filter jobs by skills.
     *
     * @param Builder<Job> $query
     * @param array<int> $skillIds
     * @return Builder<Job>
     */
    public function scopeHasSkills(Builder $query, array $skillIds): Builder
    {
        return $query->whereHas('skills', function ($q) use ($skillIds) {
            $q->whereIn('skill_id', $skillIds);
        }, '=', count($skillIds));
    }

    /**
     * Scope a query to filter jobs by at least one skill.
     *
     * @param Builder<Job> $query
     * @param array<int> $skillIds
     * @return Builder<Job>
     */
    public function scopeHasAnySkill(Builder $query, array $skillIds): Builder
    {
        return $query->whereHas('skills', function ($q) use ($skillIds) {
            $q->whereIn('skill_id', $skillIds);
        });
    }

    /**
     * Increment the views count.
     *
     * @return void
     */
    public function incrementViewsCount(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment the applications count.
     *
     * @return void
     */
    public function incrementApplicationsCount(): void
    {
        $this->increment('applications_count');
    }

    /**
     * Check if the job is bookmarked by a user.
     *
     * @param int $userId
     * @return bool
     */
    public function isBookmarkedBy(int $userId): bool
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    /**
     * Check if a user has applied to this job.
     *
     * @param int $userId
     * @return bool
     */
    public function hasUserApplied(int $userId): bool
    {
        return $this->applications()
            ->whereHas('candidate', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->exists();
    }

    /**
     * Get the application status for a user.
     *
     * @param int $userId
     * @return string|null
     */
    public function getUserApplicationStatus(int $userId): ?string
    {
        $application = $this->applications()
            ->whereHas('candidate', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->first();

        return $application ? $application->status : null;
    }

    /**
     * Check if the job is eligible for application.
     *
     * @return bool
     */
    public function isEligibleForApplication(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->application_deadline && $this->application_deadline->isPast()) {
            return false;
        }

        if ($this->vacancies && $this->applications_count >= $this->vacancies) {
            return false;
        }

        return true;
    }
}


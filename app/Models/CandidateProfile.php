<?php

namespace App\Models;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CandidateProfile extends Model
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
        'bio',
        'headline',
        'summary',
        'phone',
        'website',
        'date_of_birth',
        'gender',
        'experience_years',
        'current_salary',
        'expected_salary',
        'salary_currency',
        'education_level_id',
        'address',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'is_remote_preferred',
        'is_public',
        'is_available',
        'is_featured',
        'views_count',
        'avatar',
        'cover_image',
        'video_introduction',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'github_url',
        'portfolio_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'is_remote_preferred' => 'boolean',
        'is_public' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'experience_years' => 'integer',
        'current_salary' => 'decimal:2',
        'expected_salary' => 'decimal:2',
        'gender' => GenderEnum::class,
    ];

    /**
     * Get the user that owns the candidate profile.
     *
     * @return BelongsTo<User, CandidateProfile>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the education level that owns the candidate profile.
     *
     * @return BelongsTo<EducationLevel, CandidateProfile>
     */
    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     * Get the country that owns the candidate profile.
     *
     * @return BelongsTo<Country, CandidateProfile>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state that owns the candidate profile.
     *
     * @return BelongsTo<State, CandidateProfile>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that owns the candidate profile.
     *
     * @return BelongsTo<City, CandidateProfile>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the skills for the candidate.
     *
     * @return HasMany<CandidateSkill>
     */
    public function skills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class, 'candidate_id');
    }

    /**
     * Get the educations for the candidate.
     *
     * @return HasMany<CandidateEducation>
     */
    public function educations(): HasMany
    {
        return $this->hasMany(CandidateEducation::class, 'candidate_id');
    }

    /**
     * Get the experiences for the candidate.
     *
     * @return HasMany<CandidateExperience>
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(CandidateExperience::class, 'candidate_id');
    }

    /**
     * Get the projects for the candidate.
     *
     * @return HasMany<CandidateProject>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(CandidateProject::class, 'candidate_id');
    }

    /**
     * Get the certifications for the candidate.
     *
     * @return HasMany<CandidateCertification>
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(CandidateCertification::class, 'candidate_id');
    }

    /**
     * Get the languages for the candidate.
     *
     * @return HasMany<CandidateLanguage>
     */
    public function languages(): HasMany
    {
        return $this->hasMany(CandidateLanguage::class, 'candidate_id');
    }

    /**
     * Get the resumes for the candidate.
     *
     * @return HasMany<CandidateResume>
     */
    public function resumes(): HasMany
    {
        return $this->hasMany(CandidateResume::class, 'candidate_id');
    }

    /**
     * Get the applications for the candidate.
     *
     * @return HasMany<JobApplication>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'candidate_id');
    }

    /**
     * Get the bookmarks for the candidate.
     *
     * @return HasMany<CandidateBookmark>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(CandidateBookmark::class, 'candidate_id');
    }

    /**
     * Get the job pools for the candidate.
     *
     * @return BelongsToMany<JobPool>
     */
    public function jobPools(): BelongsToMany
    {
        return $this->belongsToMany(JobPool::class, 'candidate_job_pools', 'candidate_id', 'job_pool_id')
            ->withPivot('status', 'notes', 'added_by_user_id')
            ->withTimestamps();
    }

    /**
     * Get the primary resume for the candidate.
     *
     * @return CandidateResume|null
     */
    public function primaryResume(): ?CandidateResume
    {
        return $this->resumes()->where('is_primary', true)->first();
    }

    /**
     * Get the age attribute.
     *
     * @return int|null
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
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
     * Scope a query to only include public profiles.
     *
     * @param Builder<CandidateProfile> $query
     * @return Builder<CandidateProfile>
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include available profiles.
     *
     * @param Builder<CandidateProfile> $query
     * @return Builder<CandidateProfile>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to only include featured profiles.
     *
     * @param Builder<CandidateProfile> $query
     * @return Builder<CandidateProfile>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by country.
     *
     * @param Builder<CandidateProfile> $query
     * @param int $countryId
     * @return Builder<CandidateProfile>
     */
    public function scopeInCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope a query to filter by state.
     *
     * @param Builder<CandidateProfile> $query
     * @param int $stateId
     * @return Builder<CandidateProfile>
     */
    public function scopeInState(Builder $query, int $stateId): Builder
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope a query to filter by city.
     *
     * @param Builder<CandidateProfile> $query
     * @param int $cityId
     * @return Builder<CandidateProfile>
     */
    public function scopeInCity(Builder $query, int $cityId): Builder
    {
        return $query->where('city_id', $cityId);
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
}


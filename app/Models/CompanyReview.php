<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CompanyReview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'rating',
        'title',
        'review',
        'pros',
        'cons',
        'is_anonymous',
        'is_approved',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:1',
        'is_anonymous' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the company that owns the review.
     *
     * @return BelongsTo<Company, CompanyReview>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user that owns the review.
     *
     * @return BelongsTo<User, CompanyReview>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reviewer name attribute.
     *
     * @return string
     */
    public function getReviewerNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }

        return $this->user->full_name;
    }

    /**
     * Get the formatted rating attribute.
     *
     * @return string
     */
    public function getFormattedRatingAttribute(): string
    {
        return number_format($this->rating, 1);
    }

    /**
     * Scope a query to only include approved reviews.
     *
     * @param Builder<CompanyReview> $query
     * @return Builder<CompanyReview>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include featured reviews.
     *
     * @param Builder<CompanyReview> $query
     * @return Builder<CompanyReview>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by rating.
     *
     * @param Builder<CompanyReview> $query
     * @param float $minRating
     * @return Builder<CompanyReview>
     */
    public function scopeMinRating(Builder $query, float $minRating): Builder
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope a query to filter by company.
     *
     * @param Builder<CompanyReview> $query
     * @param int $companyId
     * @return Builder<CompanyReview>
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }
}


<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\CompanyReview;
use App\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanyService
{
    /**
     * List companies based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Company::query()
            ->with(['user', 'companySize', 'country', 'state', 'city'])
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('industry', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['user_id']), function ($query) use ($filters) {
                $query->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['industry']), function ($query) use ($filters) {
                $query->where('industry', $filters['industry']);
            })
            ->when(isset($filters['company_size_id']), function ($query) use ($filters) {
                $query->where('company_size_id', $filters['company_size_id']);
            })
            ->when(isset($filters['country_id']), function ($query) use ($filters) {
                $query->where('country_id', $filters['country_id']);
            })
            ->when(isset($filters['state_id']), function ($query) use ($filters) {
                $query->where('state_id', $filters['state_id']);
            })
            ->when(isset($filters['city_id']), function ($query) use ($filters) {
                $query->where('city_id', $filters['city_id']);
            })
            ->when(isset($filters['is_verified']), function ($query) use ($filters) {
                $query->where('is_verified', $filters['is_verified']);
            })
            ->when(isset($filters['is_featured']), function ($query) use ($filters) {
                $query->where('is_featured', $filters['is_featured']);
            })
            ->when(isset($filters['founded_year_from']), function ($query) use ($filters) {
                $query->where('founded_year', '>=', $filters['founded_year_from']);
            })
            ->when(isset($filters['founded_year_to']), function ($query) use ($filters) {
                $query->where('founded_year', '<=', $filters['founded_year_to']);
            })
            ->when(isset($filters['sort_by']) && isset($filters['sort_direction']), function ($query) use ($filters) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }, function ($query) {
                $query->latest();
            })
            ->when(isset($filters['trashed']) && $filters['trashed'], function ($query) {
                $query->onlyTrashed();
            })
            ->paginate($perPage);
    }

    /**
     * Create a new company.
     *
     * @param array $data
     * @return Company
     */
    public function create(array $data): Company
    {
        return DB::transaction(function () use ($data) {
            // Generate slug
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            // Handle logo upload
            if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                $data['logo'] = $this->uploadLogo($data['logo']);
            }

            // Handle banner upload
            if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
                $data['banner'] = $this->uploadBanner($data['banner']);
            }

            // Create company
            $company = Company::create($data);

            return $company;
        });
    }

    /**
     * Update an existing company.
     *
     * @param Company $company
     * @param array $data
     * @return Company
     */
    public function update(Company $company, array $data): Company
    {
        return DB::transaction(function () use ($company, $data) {
            // Generate slug if name is changed
            if (isset($data['name']) && $data['name'] !== $company->name) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $company->id);
            }

            // Handle logo upload
            if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                // Delete old logo if exists
                if ($company->logo) {
                    Storage::delete('public/' . $company->logo);
                }

                $data['logo'] = $this->uploadLogo($data['logo']);
            }

            // Handle banner upload
            if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
                // Delete old banner if exists
                if ($company->banner) {
                    Storage::delete('public/' . $company->banner);
                }

                $data['banner'] = $this->uploadBanner($data['banner']);
            }

            // Update company
            $company->update($data);

            return $company;
        });
    }

    /**
     * Delete a company.
     *
     * @param Company $company
     * @return bool
     */
    public function delete(Company $company): bool
    {
        return $company->delete();
    }

    /**
     * Force delete a company.
     *
     * @param Company $company
     * @return bool
     */
    public function forceDelete(Company $company): bool
    {
        return DB::transaction(function () use ($company) {
            // Delete logo if exists
            if ($company->logo) {
                Storage::delete('public/' . $company->logo);
            }

            // Delete banner if exists
            if ($company->banner) {
                Storage::delete('public/' . $company->banner);
            }

            // Delete related records
            $company->jobs()->forceDelete();
            $company->reviews()->delete();
            $company->subscriptions()->delete();
            $company->jobPools()->forceDelete();

            return $company->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted company.
     *
     * @param Company $company
     * @return bool
     */
    public function restore(Company $company): bool
    {
        return $company->restore();
    }

    /**
     * Verify a company.
     *
     * @param Company $company
     * @return bool
     */
    public function verify(Company $company): bool
    {
        return $company->update(['is_verified' => true]);
    }

    /**
     * Unverify a company.
     *
     * @param Company $company
     * @return bool
     */
    public function unverify(Company $company): bool
    {
        return $company->update(['is_verified' => false]);
    }

    /**
     * Toggle company featured status.
     *
     * @param Company $company
     * @return bool
     */
    public function toggleFeatured(Company $company): bool
    {
        return $company->update(['is_featured' => !$company->is_featured]);
    }

    /**
     * Add a review to a company.
     *
     * @param Company $company
     * @param array $data
     * @return CompanyReview
     */
    public function addReview(Company $company, array $data): CompanyReview
    {
        return $company->reviews()->create($data);
    }

    /**
     * Get company statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => Company::query()->count(),
            'verified' => Company::query()->where('is_verified', true)->count(),
            'unverified' => Company::query()->where('is_verified', false)->count(),
            'featured' => Company::query()->where('is_featured', true)->count(),
            'by_industry' => DB::table('companies')
                ->select('industry', DB::raw('count(*) as total'))
                ->groupBy('industry')
                ->get(),
            'by_company_size' => DB::table('companies')
                ->join('company_sizes', 'companies.company_size_id', '=', 'company_sizes.id')
                ->select('company_sizes.name', DB::raw('count(*) as total'))
                ->groupBy('company_sizes.name')
                ->get(),
            'by_country' => DB::table('companies')
                ->join('countries', 'companies.country_id', '=', 'countries.id')
                ->select('countries.name', DB::raw('count(*) as total'))
                ->groupBy('countries.name')
                ->get(),
            'new_today' => Company::query()->whereDate('created_at', Carbon::today())->count(),
            'new_this_week' => Company::query()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'new_this_month' => Company::query()->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * Get company analytics.
     *
     * @param Company $company
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getCompanyAnalytics(Company $company, ?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $jobs = $company->jobs()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $jobIds = $jobs->pluck('id')->toArray();

        $applications = DB::table('job_applications')
            ->whereIn('job_id', $jobIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'total_jobs' => $jobs->count(),
            'active_jobs' => $jobs->where('is_active', true)->count(),
            'featured_jobs' => $jobs->where('is_featured', true)->count(),
            'urgent_jobs' => $jobs->where('is_urgent', true)->count(),
            'total_views' => $jobs->sum('views_count'),
            'total_applications' => $applications->count(),
            'application_rate' => $jobs->sum('views_count') > 0
                ? round(($applications->count() / $jobs->sum('views_count')) * 100, 2)
                : 0,
            'jobs_by_category' => $jobs->groupBy('category_id')
                ->map(function ($items, $key) {
                    return [
                        'category_id' => $key,
                        'category_name' => $items->first()->category->name,
                        'count' => $items->count(),
                    ];
                })->values(),
            'applications_by_status' => DB::table('job_applications')
                ->whereIn('job_id', $jobIds)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'applications_over_time' => DB::table('job_applications')
                ->whereIn('job_id', $jobIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'reviews' => [
                'total' => $company->reviews()->count(),
                'average_rating' => $company->reviews()->avg('rating'),
                'by_rating' => DB::table('company_reviews')
                    ->where('company_id', $company->id)
                    ->select(DB::raw('FLOOR(rating) as rating'), DB::raw('count(*) as count'))
                    ->groupBy('rating')
                    ->get(),
            ],
        ];
    }

    /**
     * Generate a unique slug for a company.
     *
     * @param string $name
     * @param int|null $excludeId
     * @return string
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $count = Company::query()->where('slug', 'like', "{$slug}%")
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Upload a logo.
     *
     * @param UploadedFile $file
     * @return string
     */
    private function uploadLogo(UploadedFile $file): string
    {
        $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('companies', $filename, 'public');
    }

    /**
     * Upload a banner.
     *
     * @param UploadedFile $file
     * @return string
     */
    private function uploadBanner(UploadedFile $file): string
    {
        $filename = 'banner_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('companies', $filename, 'public');
    }

    /**
     * Get popular companies.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularCompanies(int $limit = 10): Collection
    {
        return Company::query()
            ->with(['companySize', 'country'])
            ->where('is_verified', true)
            ->withCount(['jobs' => function ($query) {
                $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('application_deadline')
                          ->orWhere('application_deadline', '>=', now());
                    });
            }])
            ->orderBy('jobs_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get top rated companies.
     *
     * @param int $limit
     * @return Collection
     */
    public function getTopRatedCompanies(int $limit = 10): Collection
    {
        return Company::query()
            ->with(['companySize', 'country'])
            ->where('is_verified', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->having('reviews_count', '>=', 3) // Minimum number of reviews
            ->orderBy('reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get featured companies.
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedCompanies(int $limit = 10): Collection
    {
        return Company::query()
            ->with(['companySize', 'country'])
            ->where('is_verified', true)
            ->where('is_featured', true)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get companies by industry.
     *
     * @param string $industry
     * @param int $limit
     * @return Collection
     */
    public function getCompaniesByIndustry(string $industry, int $limit = 10): Collection
    {
        return Company::query()
            ->with(['companySize', 'country'])
            ->where('is_verified', true)
            ->where('industry', $industry)
            ->latest()
            ->take($limit)
            ->get();
    }
}


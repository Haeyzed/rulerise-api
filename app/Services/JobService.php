<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobSkill;
use App\Models\Company;
use App\Models\Skill;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobService
{
    /**
     * List jobs based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Job::query()
            ->with(['company', 'category', 'jobType', 'experienceLevel', 'educationLevel'])
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhereHas('company', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when(isset($filters['company_id']), function ($query) use ($filters) {
                $query->where('company_id', $filters['company_id']);
            })
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            })
            ->when(isset($filters['job_type_id']), function ($query) use ($filters) {
                $query->where('job_type_id', $filters['job_type_id']);
            })
            ->when(isset($filters['experience_level_id']), function ($query) use ($filters) {
                $query->where('experience_level_id', $filters['experience_level_id']);
            })
            ->when(isset($filters['education_level_id']), function ($query) use ($filters) {
                $query->where('education_level_id', $filters['education_level_id']);
            })
            ->when(isset($filters['location']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('location', 'like', "%{$filters['location']}%")
                      ->orWhere('city', 'like', "%{$filters['location']}%")
                      ->orWhere('state', 'like', "%{$filters['location']}%")
                      ->orWhere('country', 'like', "%{$filters['location']}%");
                });
            })
            ->when(isset($filters['country']), function ($query) use ($filters) {
                $query->where('country', $filters['country']);
            })
            ->when(isset($filters['is_remote']), function ($query) use ($filters) {
                $query->where('is_remote', $filters['is_remote']);
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->when(isset($filters['is_featured']), function ($query) use ($filters) {
                $query->where('is_featured', $filters['is_featured']);
            })
            ->when(isset($filters['is_urgent']), function ($query) use ($filters) {
                $query->where('is_urgent', $filters['is_urgent']);
            })
            ->when(isset($filters['min_salary']), function ($query) use ($filters) {
                $query->where('min_salary', '>=', $filters['min_salary']);
            })
            ->when(isset($filters['max_salary']), function ($query) use ($filters) {
                $query->where('max_salary', '<=', $filters['max_salary']);
            })
            ->when(isset($filters['salary_currency']), function ($query) use ($filters) {
                $query->where('salary_currency', $filters['salary_currency']);
            })
            ->when(isset($filters['salary_period']), function ($query) use ($filters) {
                $query->where('salary_period', $filters['salary_period']);
            })
            ->when(isset($filters['posted_within']), function ($query) use ($filters) {
                $days = (int) $filters['posted_within'];
                $query->where('created_at', '>=', now()->subDays($days));
            })
            ->when(isset($filters['deadline_after']), function ($query) use ($filters) {
                $query->where('application_deadline', '>=', $filters['deadline_after']);
            })
            ->when(isset($filters['application_deadline']), function ($query) use ($filters) {
                $query->where('application_deadline', '>=', $filters['application_deadline']);
            })
            ->when(isset($filters['skill_ids']), function ($query) use ($filters) {
                $query->whereHas('skills', function ($q) use ($filters) {
                    $q->whereIn('skill_id', $filters['skill_ids']);
                }, '=', count($filters['skill_ids']));
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
     * Create a new job.
     *
     * @param array $data
     * @return Job
     */
    public function create(array $data): Job
    {
        return DB::transaction(function () use ($data) {
            // Generate slug
            $data['slug'] = $this->generateUniqueSlug($data['title']);

            // Create job
            $job = Job::query()->create($data);

            // Attach skills if provided
            if (isset($data['skill_ids']) && is_array($data['skill_ids'])) {
                $this->syncJobSkills($job, $data['skill_ids']);
            }

            // Check if company has subscription and update usage
            if (isset($data['company_id'])) {
                $this->updateCompanySubscriptionUsage($data['company_id'], $job->is_featured);
            }

            return $job;
        });
    }

    /**
     * Update an existing job.
     *
     * @param Job $job
     * @param array $data
     * @return Job
     */
    public function update(Job $job, array $data): Job
    {
        return DB::transaction(function () use ($job, $data) {
            $oldFeaturedStatus = $job->is_featured;

            // Generate slug if title is changed
            if (isset($data['title']) && $data['title'] !== $job->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $job->id);
            }

            // Update job
            $job->update($data);

            // Sync skills if provided
            if (isset($data['skill_ids']) && is_array($data['skill_ids'])) {
                $this->syncJobSkills($job, $data['skill_ids']);
            }

            // Check if featured status changed and update subscription usage
            if (isset($data['is_featured']) && $data['is_featured'] !== $oldFeaturedStatus) {
                $this->updateCompanySubscriptionUsage($job->company_id, $data['is_featured']);
            }

            return $job;
        });
    }

    /**
     * Delete a job.
     *
     * @param Job $job
     * @return bool
     */
    public function delete(Job $job): bool
    {
        return $job->delete();
    }

    /**
     * Force delete a job.
     *
     * @param Job $job
     * @return bool
     */
    public function forceDelete(Job $job): bool
    {
        return DB::transaction(function () use ($job) {
            // Delete related records
            $job->skills()->detach();

            return $job->forceDelete();
        });
    }

    /**
     * Restore a soft-deleted job.
     *
     * @param Job $job
     * @return bool
     */
    public function restore(Job $job): bool
    {
        return $job->restore();
    }

    /**
     * Change job status.
     *
     * @param Job $job
     * @param bool $isActive
     * @return bool
     */
    public function changeStatus(Job $job, bool $isActive): bool
    {
        return $job->update([
            'is_active' => $isActive,
        ]);
    }

    /**
     * Toggle job featured status.
     *
     * @param Job $job
     * @return bool
     * @throws Exception
     */
    public function toggleFeatured(Job $job): bool
    {
        $newStatus = !$job->is_featured;

        // Check if company has available featured jobs in subscription
        if ($newStatus) {
            $company = $job->company;
            $subscription = $company->activeSubscription();

            if ($subscription) {
                $featuredJobsLimit = $subscription->plan->featured_jobs_limit;
                $featuredJobsUsed = $subscription->featured_jobs_used;

                if ($featuredJobsLimit !== null && $featuredJobsUsed >= $featuredJobsLimit) {
                    throw new Exception('Featured jobs limit reached in current subscription');
                }
            }
        }

        $result = $job->update([
            'is_featured' => $newStatus,
        ]);

        // Update subscription usage
        if ($result) {
            $this->updateCompanySubscriptionUsage($job->company_id, $newStatus);
        }

        return $result;
    }

    /**
     * Toggle job urgent status.
     *
     * @param Job $job
     * @return bool
     */
    public function toggleUrgent(Job $job): bool
    {
        return $job->update([
            'is_urgent' => !$job->is_urgent,
        ]);
    }

    /**
     * Extend job application deadline.
     *
     * @param Job $job
     * @param DateTime $newDeadline
     * @return bool
     * @throws Exception
     */
    public function extendDeadline(Job $job, DateTime $newDeadline): bool
    {
        // Ensure new deadline is in the future
        if ($newDeadline <= now()) {
            throw new Exception('New deadline must be in the future');
        }

        // Ensure new deadline is after current deadline
        if ($job->application_deadline && $newDeadline <= $job->application_deadline) {
            throw new Exception('New deadline must be after current deadline');
        }

        return $job->update([
            'application_deadline' => $newDeadline,
        ]);
    }

    /**
     * Get similar jobs.
     *
     * @param Job $job
     * @param int $limit
     * @return Collection
     */
    public function getSimilarJobs(Job $job, int $limit = 5): Collection
    {
        // Get job skills
        $skillIds = $job->skills()->pluck('skill_id')->toArray();

        // Find similar jobs based on category, job type, and skills
        return Job::query()
            ->active()
            ->where('id', '!=', $job->id)
            ->where(function ($query) use ($job) {
                $query->where('category_id', $job->category_id)
                    ->orWhere('job_type_id', $job->job_type_id)
                    ->orWhere('experience_level_id', $job->experience_level_id);
            })
            ->when($skillIds, function ($query) use ($skillIds) {
                $query->whereHas('skills', function ($q) use ($skillIds) {
                    $q->whereIn('skill_id', $skillIds);
                });
            })
            ->with(['company:id,name,logo', 'jobType:id,name'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get job statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => Job::query()->count(),
            'active' => Job::query()->where('is_active', true)->count(),
            'inactive' => Job::query()->where('is_active', false)->count(),
            'featured' => Job::query()->where('is_featured', true)->count(),
            'urgent' => Job::query()->where('is_urgent', true)->count(),
            'remote' => Job::query()->where('is_remote', true)->count(),
            'expiring_soon' => Job::query()->where('application_deadline', '<=', now()->addDays(7))
                ->where('application_deadline', '>', now())
                ->count(),
            'expired' => Job::query()->where('application_deadline', '<', now())->count(),
            'new_today' => Job::query()->whereDate('created_at', Carbon::today())->count(),
            'new_this_week' => Job::query()->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'new_this_month' => Job::query()->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'by_category' => DB::table('jobs')
                ->join('job_categories', 'jobs.category_id', '=', 'job_categories.id')
                ->select('job_categories.name', DB::raw('count(*) as total'))
                ->groupBy('job_categories.name')
                ->get(),
            'by_job_type' => DB::table('jobs')
                ->join('job_types', 'jobs.job_type_id', '=', 'job_types.id')
                ->select('job_types.name', DB::raw('count(*) as total'))
                ->groupBy('job_types.name')
                ->get(),
        ];
    }

    /**
     * Get trending skills based on job postings.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTrendingSkills(int $limit = 10): \Illuminate\Support\Collection
    {
        return DB::table('job_skills')
            ->join('skills', 'job_skills.skill_id', '=', 'skills.id')
            ->join('jobs', 'job_skills.job_id', '=', 'jobs.id')
            ->where('jobs.created_at', '>=', now()->subDays(30))
            ->select('skills.id', 'skills.name', DB::raw('count(*) as job_count'))
            ->groupBy('skills.id', 'skills.name')
            ->orderBy('job_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if company can post more jobs based on subscription.
     *
     * @param int $companyId
     * @return bool
     */
    public function canCompanyPostMoreJobs(int $companyId): bool
    {
        $company = Company::query()->findOrFail($companyId);
        $subscription = $company->activeSubscription();

        if (!$subscription) {
            return false;
        }

        $jobPostsLimit = $subscription->plan->job_posts_limit;
        $jobPostsUsed = $subscription->job_posts_used;

        return $jobPostsLimit === null || $jobPostsUsed < $jobPostsLimit;
    }

    /**
     * Update company subscription usage.
     *
     * @param int $companyId
     * @param bool $isFeatured
     * @return void
     */
    private function updateCompanySubscriptionUsage(int $companyId, bool $isFeatured): void
    {
        $company = Company::query()->findOrFail($companyId);
        $subscription = $company->activeSubscription();

        if ($subscription) {
            // Increment job posts used
            $subscription->increment('job_posts_used');

            // Increment featured jobs used if applicable
            if ($isFeatured) {
                $subscription->increment('featured_jobs_used');
            }
        }
    }

    /**
     * Generate a unique slug for a job.
     *
     * @param string $title
     * @param int|null $excludeId
     * @return string
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $count = Job::query()->where('slug', 'like', "{$slug}%")
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Sync job skills.
     *
     * @param Job $job
     * @param array $skillIds
     * @return void
     */
    private function syncJobSkills(Job $job, array $skillIds): void
    {
        $job->skills()->sync($skillIds);
    }

    /**
     * Get job analytics for a company.
     *
     * @param int $companyId
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getCompanyJobAnalytics(int $companyId, ?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $jobs = Job::query()->where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $jobIds = $jobs->pluck('id')->toArray();

        return [
            'total_jobs' => $jobs->count(),
            'active_jobs' => $jobs->where('is_active', true)->count(),
            'featured_jobs' => $jobs->where('is_featured', true)->count(),
            'urgent_jobs' => $jobs->where('is_urgent', true)->count(),
            'total_views' => $jobs->sum('views_count'),
            'total_applications' => $jobs->sum('applications_count'),
            'application_rate' => $jobs->sum('views_count') > 0
                ? round(($jobs->sum('applications_count') / $jobs->sum('views_count')) * 100, 2)
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
        ];
    }
}


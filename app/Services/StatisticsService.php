<?php

namespace App\Services;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\BlogPost;
use App\Models\CompanyReview;
use App\Enums\UserRoleEnum;
use App\Enums\ApplicationStatusEnum;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get dashboard statistics.
     *
     * @return array
     */
    public function getDashboardStatistics(): array
    {
        return [
            'users' => [
                'total' => User::query()->count(),
                'candidates' => User::query()->where('role', UserRoleEnum::CANDIDATE->value)->count(),
                'employers' => User::query()->where('role', UserRoleEnum::EMPLOYER->value)->count(),
                'admins' => User::query()->where('role', UserRoleEnum::ADMIN->value)->count(),
                'new_today' => User::query()->whereDate('created_at', Carbon::today())->count(),
            ],
            'jobs' => [
                'total' => Job::query()->count(),
                'active' => Job::query()->where('is_active', true)->count(),
                'featured' => Job::query()->where('is_featured', true)->count(),
                'urgent' => Job::query()->where('is_urgent', true)->count(),
                'new_today' => Job::query()->whereDate('created_at', Carbon::today())->count(),
            ],
            'companies' => [
                'total' => Company::query()->count(),
                'verified' => Company::query()->where('is_verified', true)->count(),
                'featured' => Company::query()->where('is_featured', true)->count(),
                'new_today' => Company::query()->whereDate('created_at', Carbon::today())->count(),
            ],
            'candidates' => [
                'total' => CandidateProfile::query()->count(),
                'public' => CandidateProfile::query()->where('is_public', true)->count(),
                'available' => CandidateProfile::query()->where('is_available', true)->count(),
                'featured' => CandidateProfile::query()->where('is_featured', true)->count(),
                'new_today' => CandidateProfile::query()->whereDate('created_at', Carbon::today())->count(),
            ],
            'applications' => [
                'total' => JobApplication::query()->count(),
                'pending' => JobApplication::query()->where('status', ApplicationStatusEnum::PENDING->value)->count(),
                'shortlisted' => JobApplication::query()->where('status', ApplicationStatusEnum::SHORTLISTED->value)->count(),
                'hired' => JobApplication::query()->where('status', ApplicationStatusEnum::HIRED->value)->count(),
                'new_today' => JobApplication::query()->whereDate('created_at', Carbon::today())->count(),
            ],
            'blog_posts' => [
                'total' => BlogPost::query()->count(),
                'published' => BlogPost::query()->where('is_published', true)->count(),
                'new_today' => BlogPost::query()->whereDate('created_at', Carbon::today())->count(),
            ],
            'reviews' => [
                'total' => CompanyReview::query()->count(),
                'approved' => CompanyReview::query()->where('is_approved', true)->count(),
                'new_today' => CompanyReview::query()->whereDate('created_at', Carbon::today())->count(),
            ],
        ];
    }

    /**
     * Get user statistics.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getUserStatistics(?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return [
            'total' => User::query()->count(),
            'active' => User::query()->where('is_active', true)->count(),
            'inactive' => User::query()->where('is_active', false)->count(),
            'verified' => User::query()->whereNotNull('email_verified_at')->count(),
            'unverified' => User::query()->whereNull('email_verified_at')->count(),
            'by_role' => [
                'candidates' => User::query()->where('role', UserRoleEnum::CANDIDATE->value)->count(),
                'employers' => User::query()->where('role', UserRoleEnum::EMPLOYER->value)->count(),
                'admins' => User::query()->where('role', UserRoleEnum::ADMIN->value)->count(),
            ],
            'registrations_over_time' => DB::table('users')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'by_country' => DB::table('users')
                ->join('candidate_profiles', 'users.id', '=', 'candidate_profiles.user_id')
                ->join('countries', 'candidate_profiles.country_id', '=', 'countries.id')
                ->select('countries.name', DB::raw('count(*) as count'))
                ->groupBy('countries.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get job statistics.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getJobStatistics(?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return [
            'total' => Job::query()->count(),
            'active' => Job::query()->where('is_active', true)->count(),
            'inactive' => Job::query()->where('is_active', false)->count(),
            'featured' => Job::query()->where('is_featured', true)->count(),
            'urgent' => Job::query()->where('is_urgent', true)->count(),
            'remote' => Job::query()->where('is_remote', true)->count(),
            'by_category' => DB::table('jobs')
                ->join('job_categories', 'jobs.category_id', '=', 'job_categories.id')
                ->select('job_categories.name', DB::raw('count(*) as count'))
                ->groupBy('job_categories.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_type' => DB::table('jobs')
                ->join('job_types', 'jobs.job_type_id', '=', 'job_types.id')
                ->select('job_types.name', DB::raw('count(*) as count'))
                ->groupBy('job_types.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_experience_level' => DB::table('jobs')
                ->join('experience_levels', 'jobs.experience_level_id', '=', 'experience_levels.id')
                ->select('experience_levels.name', DB::raw('count(*) as count'))
                ->groupBy('experience_levels.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_country' => DB::table('jobs')
                ->join('countries', 'jobs.country_id', '=', 'countries.id')
                ->select('countries.name', DB::raw('count(*) as count'))
                ->groupBy('countries.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'postings_over_time' => DB::table('jobs')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'top_skills' => DB::table('job_skills')
                ->join('skills', 'job_skills.skill_id', '=', 'skills.id')
                ->select('skills.name', DB::raw('count(*) as count'))
                ->groupBy('skills.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get application statistics.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getApplicationStatistics(?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $stats = [
            'total' => JobApplication::query()->count(),
            'by_status' => [
                'pending' => JobApplication::query()->where('status', ApplicationStatusEnum::PENDING->value)->count(),
                'reviewed' => JobApplication::query()->where('status', ApplicationStatusEnum::REVIEWED->value)->count(),
                'shortlisted' => JobApplication::query()->where('status', ApplicationStatusEnum::SHORTLISTED->value)->count(),
                'rejected' => JobApplication::query()->where('status', ApplicationStatusEnum::REJECTED->value)->count(),
                'interview' => JobApplication::query()->where('status', ApplicationStatusEnum::INTERVIEW->value)->count(),
                'offered' => JobApplication::query()->where('status', ApplicationStatusEnum::OFFERED->value)->count(),
                'hired' => JobApplication::query()->where('status', ApplicationStatusEnum::HIRED->value)->count(),
                'withdrawn' => JobApplication::query()->where('status', ApplicationStatusEnum::WITHDRAWN->value)->count(),
            ],
            'viewed' => JobApplication::query()->where('is_viewed', true)->count(),
            'unviewed' => JobApplication::query()->where('is_viewed', false)->count(),
            'applications_over_time' => DB::table('job_applications')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'by_job_category' => DB::table('job_applications')
                ->join('jobs', 'job_applications.job_id', '=', 'jobs.id')
                ->join('job_categories', 'jobs.category_id', '=', 'job_categories.id')
                ->select('job_categories.name', DB::raw('count(*) as count'))
                ->groupBy('job_categories.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'by_company' => DB::table('job_applications')
                ->join('jobs', 'job_applications.job_id', '=', 'jobs.id')
                ->join('companies', 'jobs.company_id', '=', 'companies.id')
                ->select('companies.name', DB::raw('count(*) as count'))
                ->groupBy('companies.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'conversion_rate' => DB::table('jobs')
                ->select(DB::raw('SUM(views_count) as total_views'), DB::raw('SUM(applications_count) as total_applications'))
                ->first(),
        ];

        // Calculate conversion rate
        if ($stats['conversion_rate']->total_views > 0) {
            $stats['conversion_rate'] = round(($stats['conversion_rate']->total_applications / $stats['conversion_rate']->total_views) * 100, 2);
        } else {
            $stats['conversion_rate'] = 0;
        }

        return $stats;
    }

    /**
     * Get company statistics.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getCompanyStatistics(?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return [
            'total' => Company::query()->count(),
            'verified' => Company::query()->where('is_verified', true)->count(),
            'unverified' => Company::query()->where('is_verified', false)->count(),
            'featured' => Company::query()->where('is_featured', true)->count(),
            'by_industry' => DB::table('companies')
                ->select('industry', DB::raw('count(*) as count'))
                ->groupBy('industry')
                ->orderBy('count', 'desc')
                ->get(),
            'by_company_size' => DB::table('companies')
                ->join('company_sizes', 'companies.company_size_id', '=', 'company_sizes.id')
                ->select('company_sizes.name', DB::raw('count(*) as count'))
                ->groupBy('company_sizes.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_country' => DB::table('companies')
                ->join('countries', 'companies.country_id', '=', 'countries.id')
                ->select('countries.name', DB::raw('count(*) as count'))
                ->groupBy('countries.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'registrations_over_time' => DB::table('companies')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'top_hiring' => DB::table('companies')
                ->join('jobs', 'companies.id', '=', 'jobs.company_id')
                ->select('companies.name', DB::raw('count(jobs.id) as job_count'))
                ->where('jobs.is_active', true)
                ->groupBy('companies.name')
                ->orderBy('job_count', 'desc')
                ->limit(10)
                ->get(),
            'reviews' => [
                'total' => CompanyReview::query()->count(),
                'approved' => CompanyReview::query()->where('is_approved', true)->count(),
                'featured' => CompanyReview::query()->where('is_featured', true)->count(),
                'average_rating' => CompanyReview::query()->avg('rating'),
            ],
        ];
    }

    /**
     * Get candidate statistics.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getCandidateStatistics(?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return [
            'total' => CandidateProfile::query()->count(),
            'public' => CandidateProfile::query()->where('is_public', true)->count(),
            'private' => CandidateProfile::query()->where('is_public', false)->count(),
            'available' => CandidateProfile::query()->where('is_available', true)->count(),
            'unavailable' => CandidateProfile::query()->where('is_available', false)->count(),
            'featured' => CandidateProfile::query()->where('is_featured', true)->count(),
            'remote_preferred' => CandidateProfile::query()->where('is_remote_preferred', true)->count(),
            'by_experience' => [
                'entry_level' => CandidateProfile::query()->where('experience_years', '<', 2)->count(),
                'mid_level' => CandidateProfile::query()->whereBetween('experience_years', [2, 5])->count(),
                'senior' => CandidateProfile::query()->where('experience_years', '>', 5)->count(),
            ],
            'by_education_level' => DB::table('candidate_profiles')
                ->join('education_levels', 'candidate_profiles.education_level_id', '=', 'education_levels.id')
                ->select('education_levels.name', DB::raw('count(*) as count'))
                ->groupBy('education_levels.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_country' => DB::table('candidate_profiles')
                ->join('countries', 'candidate_profiles.country_id', '=', 'countries.id')
                ->select('countries.name', DB::raw('count(*) as count'))
                ->groupBy('countries.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'registrations_over_time' => DB::table('candidate_profiles')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'top_skills' => DB::table('candidate_skills')
                ->join('skills', 'candidate_skills.skill_id', '=', 'skills.id')
                ->select('skills.name', DB::raw('count(*) as count'))
                ->groupBy('skills.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get blog statistics.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @return array
     */
    public function getBlogStatistics(?DateTime $startDate = null, ?DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        return [
            'total' => BlogPost::query()->count(),
            'published' => BlogPost::query()->where('is_published', true)->count(),
            'draft' => BlogPost::query()->where('is_published', false)->count(),
            'total_views' => BlogPost::query()->sum('views_count'),
            'by_category' => DB::table('blog_posts')
                ->join('blog_categories', 'blog_posts.category_id', '=', 'blog_categories.id')
                ->select('blog_categories.name', DB::raw('count(*) as count'))
                ->groupBy('blog_categories.name')
                ->orderBy('count', 'desc')
                ->get(),
            'by_author' => DB::table('blog_posts')
                ->join('users', 'blog_posts.user_id', '=', 'users.id')
                ->select(
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as author_name'),
                    DB::raw('count(*) as count')
                )
                ->groupBy('users.id', 'users.first_name', 'users.last_name')
                ->orderBy('count', 'desc')
                ->get(),
            'posts_over_time' => DB::table('blog_posts')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'popular_tags' => DB::table('blog_post_tags')
                ->join('blog_tags', 'blog_post_tags.tag_id', '=', 'blog_tags.id')
                ->select('blog_tags.name', DB::raw('count(*) as count'))
                ->groupBy('blog_tags.name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'most_viewed_posts' => BlogPost::query()->where('is_published', true)
                ->orderBy('views_count', 'desc')
                ->take(10)
                ->get(['id', 'title', 'views_count']),
        ];
    }
}


<?php

namespace App\Services;

use App\Models\Job;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\BlogPost;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchService
{
    /**
     * Search for jobs based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchJobs(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Job::query()
            ->with(['company', 'category', 'jobType', 'experienceLevel', 'educationLevel', 'skills'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('application_deadline')
                    ->orWhere('application_deadline', '>=', now());
            });

        // Apply keyword search
        if (!empty($filters['keywords'])) {
            $keywords = $filters['keywords'];
            $query->where(function ($q) use ($keywords) {
                $q->where('title', 'like', "%{$keywords}%")
                    ->orWhere('description', 'like', "%{$keywords}%")
                    ->orWhere('requirements', 'like', "%{$keywords}%")
                    ->orWhere('responsibilities', 'like', "%{$keywords}%")
                    ->orWhere('benefits', 'like', "%{$keywords}%")
                    ->orWhereHas('company', function ($sq) use ($keywords) {
                        $sq->where('name', 'like', "%{$keywords}%");
                    });
            });
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply job type filter
        if (!empty($filters['job_type_id'])) {
            $query->where('job_type_id', $filters['job_type_id']);
        }

        // Apply experience level filter
        if (!empty($filters['experience_level_id'])) {
            $query->where('experience_level_id', $filters['experience_level_id']);
        }

        // Apply education level filter
        if (!empty($filters['education_level_id'])) {
            $query->where('education_level_id', $filters['education_level_id']);
        }

        // Apply location filter
        if (!empty($filters['location'])) {
            $query->location($filters['location']);
        }

        // Apply country filter
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Apply remote filter
        if (isset($filters['is_remote']) && $filters['is_remote']) {
            $query->where('is_remote', true);
        }

        // Apply salary range filter
        if (!empty($filters['min_salary'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('min_salary', '>=', $filters['min_salary'])
                    ->orWhere('max_salary', '>=', $filters['min_salary']);
            });
        }

        if (!empty($filters['max_salary'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('max_salary', '<=', $filters['max_salary'])
                    ->orWhereNull('max_salary');
            });
        }

        // Apply skills filter
        if (!empty($filters['skill_ids']) && is_array($filters['skill_ids'])) {
            $query->whereHas('skills', function ($q) use ($filters) {
                $q->whereIn('skill_id', $filters['skill_ids']);
            });
        }

        // Apply company filter
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        // Apply posted within filter
        if (!empty($filters['posted_within'])) {
            $query->where('created_at', '>=', now()->subDays($filters['posted_within']));
        }

        // Apply featured filter
        if (isset($filters['is_featured']) && $filters['is_featured']) {
            $query->where('is_featured', true);
        }

        // Apply urgent filter
        if (isset($filters['is_urgent']) && $filters['is_urgent']) {
            $query->where('is_urgent', true);
        }

        // Apply sorting
        if (!empty($filters['sort_by'])) {
            $sortDirection = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'desc';

            switch ($filters['sort_by']) {
                case 'title':
                case 'created_at':
                case 'min_salary':
                case 'max_salary':
                case 'views_count':
                case 'applications_count':
                    $query->orderBy($filters['sort_by'], $sortDirection);
                    break;
                case 'relevance':
                    // If keywords are provided, relevance is already applied
                    if (empty($filters['keywords'])) {
                        $query->latest();
                    }
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Search for candidates based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchCandidates(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = CandidateProfile::query()
            ->with(['user', 'educationLevel', 'skills.skill', 'educations', 'experiences'])
            ->where('is_public', true)
            ->where('is_available', true);

        // Apply keyword search
        if (!empty($filters['keywords'])) {
            $keywords = $filters['keywords'];
            $query->where(function ($q) use ($keywords) {
                $q->where('title', 'like', "%{$keywords}%")
                    ->orWhere('bio', 'like', "%{$keywords}%")
                    ->orWhereHas('user', function ($sq) use ($keywords) {
                        $sq->where('first_name', 'like', "%{$keywords}%")
                            ->orWhere('last_name', 'like', "%{$keywords}%");
                    })
                    ->orWhereHas('skills.skill', function ($sq) use ($keywords) {
                        $sq->where('name', 'like', "%{$keywords}%");
                    })
                    ->orWhereHas('educations', function ($sq) use ($keywords) {
                        $sq->where('institution', 'like', "%{$keywords}%")
                            ->orWhere('degree', 'like', "%{$keywords}%")
                            ->orWhere('field_of_study', 'like', "%{$keywords}%");
                    })
                    ->orWhereHas('experiences', function ($sq) use ($keywords) {
                        $sq->where('company_name', 'like', "%{$keywords}%")
                            ->orWhere('job_title', 'like', "%{$keywords}%");
                    });
            });
        }

        // Apply education level filter
        if (!empty($filters['education_level_id'])) {
            $query->where('education_level_id', $filters['education_level_id']);
        }

        // Apply experience years filter
        if (!empty($filters['min_experience_years'])) {
            $query->where('experience_years', '>=', $filters['min_experience_years']);
        }

        if (!empty($filters['max_experience_years'])) {
            $query->where('experience_years', '<=', $filters['max_experience_years']);
        }

        // Apply location filter
        if (!empty($filters['location'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('city', 'like', "%{$filters['location']}%")
                    ->orWhere('state', 'like', "%{$filters['location']}%")
                    ->orWhere('country', 'like', "%{$filters['location']}%");
            });
        }

        // Apply country filter
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Apply remote preference filter
        if (isset($filters['is_remote_preferred']) && $filters['is_remote_preferred']) {
            $query->where('is_remote_preferred', true);
        }

        // Apply skills filter
        if (!empty($filters['skill_ids']) && is_array($filters['skill_ids'])) {
            $query->whereHas('skills', function ($q) use ($filters) {
                $q->whereIn('skill_id', $filters['skill_ids']);
            });
        }

        // Apply featured filter
        if (isset($filters['is_featured']) && $filters['is_featured']) {
            $query->where('is_featured', true);
        }

        // Apply sorting
        if (!empty($filters['sort_by'])) {
            $sortDirection = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'desc';

            switch ($filters['sort_by']) {
                case 'experience_years':
                case 'expected_salary':
                case 'views_count':
                case 'created_at':
                    $query->orderBy($filters['sort_by'], $sortDirection);
                    break;
                case 'relevance':
                    // If keywords are provided, relevance is already applied
                    if (empty($filters['keywords'])) {
                        $query->latest();
                    }
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Search for companies based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchCompanies(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Company::query()
            ->with(['user', 'companySize', 'jobs' => function ($q) {
                $q->where('is_active', true)
                    ->where(function ($sq) {
                        $sq->whereNull('application_deadline')
                            ->orWhere('application_deadline', '>=', now());
                    });
            }])
            ->where('is_verified', true);

        // Apply keyword search
        if (!empty($filters['keywords'])) {
            $keywords = $filters['keywords'];
            $query->where(function ($q) use ($keywords) {
                $q->where('name', 'like', "%{$keywords}%")
                    ->orWhere('description', 'like', "%{$keywords}%")
                    ->orWhere('industry', 'like', "%{$keywords}%");
            });
        }

        // Apply industry filter
        if (!empty($filters['industry'])) {
            $query->where('industry', $filters['industry']);
        }

        // Apply company size filter
        if (!empty($filters['company_size_id'])) {
            $query->where('company_size_id', $filters['company_size_id']);
        }

        // Apply location filter
        if (!empty($filters['location'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('city', 'like', "%{$filters['location']}%")
                    ->orWhere('state', 'like', "%{$filters['location']}%")
                    ->orWhere('country', 'like', "%{$filters['location']}%");
            });
        }

        // Apply country filter
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Apply featured filter
        if (isset($filters['is_featured']) && $filters['is_featured']) {
            $query->where('is_featured', true);
        }

        // Apply founded year range filter
        if (!empty($filters['min_founded_year'])) {
            $query->where('founded_year', '>=', $filters['min_founded_year']);
        }

        if (!empty($filters['max_founded_year'])) {
            $query->where('founded_year', '<=', $filters['max_founded_year']);
        }

        // Apply sorting
        if (!empty($filters['sort_by'])) {
            $sortDirection = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'desc';

            switch ($filters['sort_by']) {
                case 'name':
                case 'founded_year':
                case 'created_at':
                    $query->orderBy($filters['sort_by'], $sortDirection);
                    break;
                case 'relevance':
                    // If keywords are provided, relevance is already applied
                    if (empty($filters['keywords'])) {
                        $query->latest();
                    }
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Search for blog posts based on given criteria.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchBlogPosts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BlogPost::query()
            ->with(['category', 'user', 'tags'])
            ->where('is_published', true)
            ->where('published_at', '<=', now());

        // Apply keyword search
        if (!empty($filters['keywords'])) {
            $keywords = $filters['keywords'];
            $query->where(function ($q) use ($keywords) {
                $q->where('title', 'like', "%{$keywords}%")
                    ->orWhere('content', 'like', "%{$keywords}%")
                    ->orWhere('excerpt', 'like', "%{$keywords}%")
                    ->orWhereHas('tags', function ($sq) use ($keywords) {
                        $sq->where('name', 'like', "%{$keywords}%");
                    });
            });
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply tag filter
        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('tag_id', $filters['tag_id']);
            });
        }

        // Apply author filter
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Apply date range filter
        if (!empty($filters['published_after'])) {
            $query->where('published_at', '>=', $filters['published_after']);
        }

        if (!empty($filters['published_before'])) {
            $query->where('published_at', '<=', $filters['published_before']);
        }

        // Apply sorting
        if (!empty($filters['sort_by'])) {
            $sortDirection = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'desc';

            switch ($filters['sort_by']) {
                case 'title':
                case 'published_at':
                case 'views_count':
                    $query->orderBy($filters['sort_by'], $sortDirection);
                    break;
                case 'relevance':
                    // If keywords are provided, relevance is already applied
                    if (empty($filters['keywords'])) {
                        $query->latest('published_at');
                    }
                    break;
                default:
                    $query->latest('published_at');
                    break;
            }
        } else {
            $query->latest('published_at');
        }

        return $query->paginate($perPage);
    }

    /**
     * Perform a global search across multiple entities.
     *
     * @param string $keywords
     * @param int $limit
     * @return array
     */
    public function globalSearch(string $keywords, int $limit = 5): array
    {
        return [
            'jobs' => $this->searchJobs(['keywords' => $keywords], $limit)->items(),
            'candidates' => $this->searchCandidates(['keywords' => $keywords], $limit)->items(),
            'companies' => $this->searchCompanies(['keywords' => $keywords], $limit)->items(),
            'blog_posts' => $this->searchBlogPosts(['keywords' => $keywords], $limit)->items(),
        ];
    }

    /**
     * Get popular search terms.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularSearchTerms(int $limit = 10): Collection
    {
        // This would typically be implemented with a search_logs table
        // For now, we'll return a static list of popular terms
        return collect([
            ['term' => 'Software Engineer', 'count' => 120],
            ['term' => 'Data Scientist', 'count' => 95],
            ['term' => 'Product Manager', 'count' => 87],
            ['term' => 'UX Designer', 'count' => 76],
            ['term' => 'DevOps', 'count' => 68],
            ['term' => 'Remote', 'count' => 65],
            ['term' => 'JavaScript', 'count' => 62],
            ['term' => 'Python', 'count' => 58],
            ['term' => 'React', 'count' => 52],
            ['term' => 'Machine Learning', 'count' => 48],
        ]);
    }

    /**
     * Log a search term.
     *
     * @param string $term
     * @param string $entityType
     * @param int|null $userId
     * @return void
     */
    public function logSearch(string $term, string $entityType, ?int $userId = null): void
    {
        // This would typically save to a search_logs table
        // For now, we'll just log it
        Log::info('Search', [
            'term' => $term,
            'entity_type' => $entityType,
            'user_id' => $userId,
            'timestamp' => now(),
        ]);
    }
}


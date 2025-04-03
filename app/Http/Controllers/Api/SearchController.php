<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\JobResource;
use App\Http\Resources\CandidateProfileResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\BlogPostResource;
use App\Http\Resources\SkillResource;

class SearchController extends Controller
{
    /**
     * The search service instance.
     *
     * @var SearchService
     */
    protected $searchService;

    /**
     * Create a new controller instance.
     *
     * @param SearchService $searchService
     * @return void
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Search jobs.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchJobs(Request $request): JsonResponse
    {
        $jobs = $this->searchService->searchJobs($request->all(), $request->get('per_page', 15));
        
        return response()->json([
            'data' => JobResource::collection($jobs),
            'meta' => [
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
            ],
        ]);
    }

    /**
     * Search candidates.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCandidates(Request $request): JsonResponse
    {
        $candidates = $this->searchService->searchCandidates($request->all(), $request->get('per_page', 15));
        
        return response()->json([
            'data' => CandidateProfileResource::collection($candidates),
            'meta' => [
                'total' => $candidates->total(),
                'per_page' => $candidates->perPage(),
                'current_page' => $candidates->currentPage(),
                'last_page' => $candidates->lastPage(),
            ],
        ]);
    }

    /**
     * Search companies.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCompanies(Request $request): JsonResponse
    {
        $companies = $this->searchService->searchCompanies($request->all(), $request->get('per_page', 15));
        
        return response()->json([
            'data' => CompanyResource::collection($companies),
            'meta' => [
                'total' => $companies->total(),
                'per_page' => $companies->perPage(),
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
            ],
        ]);
    }

    /**
     * Search blog posts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchBlogPosts(Request $request): JsonResponse
    {
        $blogPosts = $this->searchService->searchBlogPosts($request->all(), $request->get('per_page', 15));
        
        return response()->json([
            'data' => BlogPostResource::collection($blogPosts),
            'meta' => [
                'total' => $blogPosts->total(),
                'per_page' => $blogPosts->perPage(),
                'current_page' => $blogPosts->currentPage(),
                'last_page' => $blogPosts->lastPage(),
            ],
        ]);
    }

    /**
     * Search skills.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchSkills(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:2',
        ]);
        
        $skills = $this->searchService->searchSkills($request->keyword, $request->get('limit', 10));
        
        return response()->json([
            'data' => SkillResource::collection($skills),
        ]);
    }

    /**
     * Global search across multiple entities.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function globalSearch(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:2',
        ]);
        
        $results = $this->searchService->globalSearch($request->keyword, $request->get('limit', 5));
        
        return response()->json([
            'data' => [
                'jobs' => JobResource::collection($results['jobs']),
                'companies' => CompanyResource::collection($results['companies']),
                'candidates' => CandidateProfileResource::collection($results['candidates']),
                'blog_posts' => BlogPostResource::collection($results['blog_posts']),
                'skills' => SkillResource::collection($results['skills']),
            ],
        ]);
    }

    /**
     * Get search suggestions based on keyword.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSearchSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:2',
        ]);
        
        $suggestions = $this->searchService->getSearchSuggestions($request->keyword, $request->get('limit', 5));
        
        return response()->json([
            'data' => $suggestions,
        ]);
    }

    /**
     * Get trending searches.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrendingSearches(Request $request): JsonResponse
    {
        $trendingSearches = $this->searchService->getTrendingSearches($request->get('limit', 10));
        
        return response()->json([
            'data' => $trendingSearches,
        ]);
    }
}


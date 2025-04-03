<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Http\Resources\CandidateProfileResource;
use App\Models\Job;
use App\Services\JobService;
use App\Services\CandidateService;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * @var JobService
     */
    protected JobService $jobService;

    /**
     * @var CandidateService
     */
    protected CandidateService $candidateService;

    /**
     * JobController constructor.
     *
     * @param JobService $jobService
     * @param CandidateService $candidateService
     */
    public function __construct(JobService $jobService, CandidateService $candidateService)
    {
        $this->jobService = $jobService;
        $this->candidateService = $candidateService;
//        $this->authorizeResource(Job::class, 'job');
    }

    /**
     * Display a listing of the jobs.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search', 'company_id', 'category_id', 'job_type_id', 'experience_level_id',
            'education_level_id', 'location', 'country', 'is_remote', 'is_active',
            'is_featured', 'is_urgent', 'min_salary', 'max_salary', 'salary_currency',
            'salary_period', 'application_deadline', 'skill_ids', 'sort_by',
            'sort_direction', 'trashed', 'posted_within', 'deadline_after'
        ]);

        $jobs = $this->jobService->list($filters, $request->input('per_page', 15));

        return response()->paginatedSuccess(
            JobResource::collection($jobs),
            'Jobs retrieved successfully'
        );
    }

    /**
     * Store a newly created job in storage.
     *
     * @param JobRequest $request
     * @return JsonResponse
     */
    public function store(JobRequest $request): JsonResponse
    {
        // Check if company can post more jobs based on subscription
        if (!$this->jobService->canCompanyPostMoreJobs($request->input('company_id'))) {
            return response()->error('Job post limit reached in current subscription', 403);
        }

        $job = $this->jobService->create($request->validated());

        return response()->created(
            new JobResource($job),
            'Job created successfully'
        );
    }

    /**
     * Display the specified job.
     *
     * @param Job $job
     * @return JsonResponse
     */
    public function show(Job $job): JsonResponse
    {
        // Increment views count
        $job->incrementViewsCount();

        // Load relationships
        $job->load(['company', 'category', 'jobType', 'experienceLevel', 'educationLevel', 'skills']);

        // Get similar jobs
        $similarJobs = $this->jobService->getSimilarJobs($job);

        return response()->success(
            [
                'job' => new JobResource($job),
                'similar_jobs' => JobResource::collection($similarJobs)
            ],
            'Job retrieved successfully'
        );
    }

    /**
     * Update the specified job in storage.
     *
     * @param JobRequest $request
     * @param Job $job
     * @return JsonResponse
     */
    public function update(JobRequest $request, Job $job): JsonResponse
    {
        $job = $this->jobService->update($job, $request->validated());

        return response()->success(
            new JobResource($job),
            'Job updated successfully'
        );
    }

    /**
     * Remove the specified job from storage.
     *
     * @param Job $job
     * @return JsonResponse
     */
    public function destroy(Job $job): JsonResponse
    {
        $this->jobService->delete($job);

        return response()->success(null, 'Job deleted successfully');
    }

    /**
     * Force delete the specified job from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $job = Job::withTrashed()->findOrFail($id);
//        $this->authorize('forceDelete', $job);

        $this->jobService->forceDelete($job);

        return response()->success(null, 'Job permanently deleted successfully');
    }

    /**
     * Restore the specified job.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $job = Job::withTrashed()->findOrFail($id);
//        $this->authorize('restore', $job);

        $this->jobService->restore($job);

        return response()->success(
            new JobResource($job),
            'Job restored successfully'
        );
    }

    /**
     * Change job status.
     *
     * @param Request $request
     * @param Job $job
     * @return JsonResponse
     */
    public function changeStatus(Request $request, Job $job): JsonResponse
    {
//        $this->authorize('update', $job);

        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $this->jobService->changeStatus($job, $request->input('is_active'));

        return response()->success(
            new JobResource($job),
            'Job status changed successfully'
        );
    }

    /**
     * Toggle job featured status.
     *
     * @param Job $job
     * @return JsonResponse
     */
    public function toggleFeatured(Job $job): JsonResponse
    {
//        $this->authorize('update', $job);

        try {
            $this->jobService->toggleFeatured($job);

            return response()->success(
                new JobResource($job),
                'Job featured status toggled successfully'
            );
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 403);
        }
    }

    /**
     * Toggle job urgent status.
     *
     * @param Job $job
     * @return JsonResponse
     */
    public function toggleUrgent(Job $job): JsonResponse
    {
//        $this->authorize('update', $job);

        $this->jobService->toggleUrgent($job);

        return response()->success(
            new JobResource($job),
            'Job urgent status toggled successfully'
        );
    }

    /**
     * Extend job application deadline.
     *
     * @param Request $request
     * @param Job $job
     * @return JsonResponse
     */
    public function extendDeadline(Request $request, Job $job): JsonResponse
    {
//        $this->authorize('update', $job);

        $request->validate([
            'new_deadline' => 'required|date|after:now',
        ]);

        try {
            $this->jobService->extendDeadline($job, new DateTime($request->input('new_deadline')));

            return response()->success(
                new JobResource($job),
                'Job application deadline extended successfully'
            );
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    /**
     * Get job statistics.
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
//        $this->authorize('viewAny', Job::class);

        $stats = $this->jobService->getStatistics();

        return response()->success($stats, 'Job statistics retrieved successfully');
    }

    /**
     * Get trending skills based on job postings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrendingSkills(Request $request): JsonResponse
    {
//        $this->authorize('viewAny', Job::class);

        $limit = $request->input('limit', 10);
        $trendingSkills = $this->jobService->getTrendingSkills($limit);

        return response()->success($trendingSkills, 'Trending skills retrieved successfully');
    }

    /**
     * Get recommended candidates for a job.
     *
     * @param Job $job
     * @param Request $request
     * @return JsonResponse
     */
    public function getRecommendedCandidates(Job $job, Request $request): JsonResponse
    {
//        $this->authorize('view', $job);

        $limit = $request->input('limit', 10);
        $candidates = $this->candidateService->getRecommendedCandidatesForJob($job->id, $limit);

        return response()->success(
            CandidateProfileResource::collection($candidates),
            'Recommended candidates retrieved successfully'
        );
    }

    /**
     * Get company job analytics.
     *
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     * @throws \DateMalformedStringException
     */
    public function getCompanyJobAnalytics(Request $request, int $companyId): JsonResponse
    {
//        $this->authorize('viewAny', Job::class);

        $startDate = $request->input('start_date') ? new DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new DateTime($request->input('end_date')) : null;

        $analytics = $this->jobService->getCompanyJobAnalytics($companyId, $startDate, $endDate);

        return response()->success($analytics, 'Company job analytics retrieved successfully');
    }
}


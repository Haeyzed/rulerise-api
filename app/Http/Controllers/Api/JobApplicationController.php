<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobApplicationService;
use App\Http\Requests\JobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Http\Resources\JobApplicationStatusHistoryResource;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobApplicationController extends Controller
{
    /**
     * The job application service instance.
     *
     * @var JobApplicationService
     */
    protected $jobApplicationService;

    /**
     * Create a new controller instance.
     *
     * @param JobApplicationService $jobApplicationService
     * @return void
     */
    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->jobApplicationService = $jobApplicationService;
        $this->authorizeResource(JobApplication::class, 'application');
    }

    /**
     * Display a listing of the job applications.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'job_id', 'candidate_id', 'status', 'created_from',
            'created_to', 'sort_by', 'sort_direction'
        ]);
        
        $applications = $this->jobApplicationService->list($filters, $request->input('per_page', 15));
        
        return response()->paginatedSuccess(
            JobApplicationResource::collection($applications),
            'Job applications retrieved successfully'
        );
    }

    /**
     * Store a newly created job application in storage.
     *
     * @param JobApplicationRequest $request
     * @return JsonResponse
     */
    public function store(JobApplicationRequest $request): JsonResponse
    {
        // Check if candidate has already applied to this job
        if ($this->jobApplicationService->hasApplied($request->job_id, $request->candidate_id)) {
            return response()->error('You have already applied to this job', 422);
        }
        
        $application = $this->jobApplicationService->create($request->validated());
        
        return response()->created(
            new JobApplicationResource($application),
            'Job application submitted successfully'
        );
    }

    /**
     * Display the specified job application.
     *
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function show(JobApplication $application): JsonResponse
    {
        $application->load(['job', 'candidate.user', 'resume', 'statusHistory']);
        
        return response()->success(
            new JobApplicationResource($application),
            'Job application retrieved successfully'
        );
    }

    /**
     * Update the specified job application in storage.
     *
     * @param JobApplicationRequest $request
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function update(JobApplicationRequest $request, JobApplication $application): JsonResponse
    {
        $application = $this->jobApplicationService->update($application, $request->validated());
        
        return response()->success(
            new JobApplicationResource($application),
            'Job application updated successfully'
        );
    }

    /**
     * Remove the specified job application from storage.
     *
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function destroy(JobApplication $application): JsonResponse
    {
        $this->jobApplicationService->delete($application);
        
        return response()->success(null, 'Job application deleted successfully');
    }

    /**
     * Change job application status.
     *
     * @param Request $request
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function changeStatus(Request $request, JobApplication $application): JsonResponse
    {
        $this->authorize('update', $application);
        
        $request->validate([
            'status' => 'required|string|in:pending,reviewing,shortlisted,interview,rejected,hired,withdrawn',
            'notes' => 'nullable|string',
        ]);
        
        $application = $this->jobApplicationService->changeStatus(
            $application,
            $request->status,
            $request->notes
        );
        
        return response()->success(
            new JobApplicationResource($application),
            'Job application status changed successfully'
        );
    }

    /**
     * Get job application status history.
     *
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function getStatusHistory(JobApplication $application): JsonResponse
    {
        $this->authorize('view', $application);
        
        $statusHistory = $application->statusHistory()->orderBy('created_at', 'desc')->get();
        
        return response()->success(
            JobApplicationStatusHistoryResource::collection($statusHistory),
            'Job application status history retrieved successfully'
        );
    }

    /**
     * Add note to job application.
     *
     * @param Request $request
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function addNote(Request $request, JobApplication $application): JsonResponse
    {
        $this->authorize('update', $application);
        
        $request->validate([
            'notes' => 'required|string',
        ]);
        
        $application = $this->jobApplicationService->addNote($application, $request->notes);
        
        return response()->success(
            new JobApplicationResource($application),
            'Note added to job application successfully'
        );
    }

    /**
     * Get applications for a job.
     *
     * @param Request $request
     * @param int $jobId
     * @return JsonResponse
     */
    public function getApplicationsForJob(Request $request, int $jobId): JsonResponse
    {
        $this->authorize('viewAny', JobApplication::class);
        
        $filters = $request->only(['status', 'sort_by', 'sort_direction']);
        $filters['job_id'] = $jobId;
        
        $applications = $this->jobApplicationService->list($filters, $request->input('per_page', 15));
        
        return response()->paginatedSuccess(
            JobApplicationResource::collection($applications),
            'Job applications retrieved successfully'
        );
    }

    /**
     * Get applications for a candidate.
     *
     * @param Request $request
     * @param int $candidateId
     * @return JsonResponse
     */
    public function getApplicationsForCandidate(Request $request, int $candidateId): JsonResponse
    {
        $this->authorize('viewAny', JobApplication::class);
        
        $filters = $request->only(['status', 'sort_by', 'sort_direction']);
        $filters['candidate_id'] = $candidateId;
        
        $applications = $this->jobApplicationService->list($filters, $request->input('per_page', 15));
        
        return response()->paginatedSuccess(
            JobApplicationResource::collection($applications),
            'Job applications retrieved successfully'
        );
    }

    /**
     * Withdraw job application.
     *
     * @param JobApplication $application
     * @return JsonResponse
     */
    public function withdraw(JobApplication $application): JsonResponse
    {
        $this->authorize('update', $application);
        
        $application = $this->jobApplicationService->withdraw($application);
        
        return response()->success(
            new JobApplicationResource($application),
            'Job application withdrawn successfully'
        );
    }

    /**
     * Get application statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', JobApplication::class);
        
        $filters = $request->only(['job_id', 'candidate_id', 'date_from', 'date_to']);
        
        $statistics = $this->jobApplicationService->getStatistics($filters);
        
        return response()->success(
            $statistics,
            'Application statistics retrieved successfully'
        );
    }
}


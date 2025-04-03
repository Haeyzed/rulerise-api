<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobPoolService;
use App\Http\Requests\JobPoolRequest;
use App\Http\Requests\CandidateJobPoolRequest;
use App\Http\Resources\JobPoolResource;
use App\Http\Resources\CandidateJobPoolResource;
use App\Http\Resources\JobPoolStatusHistoryResource;
use App\Models\JobPool;
use App\Models\CandidateJobPool;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobPoolController extends Controller
{
    /**
     * The job pool service instance.
     *
     * @var JobPoolService
     */
    protected $jobPoolService;

    /**
     * Create a new controller instance.
     *
     * @param JobPoolService $jobPoolService
     * @return void
     */
    public function __construct(JobPoolService $jobPoolService)
    {
        $this->jobPoolService = $jobPoolService;
//        $this->authorizeResource(JobPool::class, 'jobPool');
    }

    /**
     * Display a listing of the job pools.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search', 'company_id', 'is_active', 'created_from',
            'created_to', 'sort_by', 'sort_direction'
        ]);

        $jobPools = $this->jobPoolService->list($filters, $request->input('per_page', 15));

        return response()->paginatedSuccess(
            JobPoolResource::collection($jobPools),
            'Job pools retrieved successfully'
        );
    }

    /**
     * Store a newly created job pool in storage.
     *
     * @param JobPoolRequest $request
     * @return JsonResponse
     */
    public function store(JobPoolRequest $request): JsonResponse
    {
        $jobPool = $this->jobPoolService->create($request->validated());

        return response()->created(
            new JobPoolResource($jobPool),
            'Job pool created successfully'
        );
    }

    /**
     * Display the specified job pool.
     *
     * @param JobPool $jobPool
     * @return JsonResponse
     */
    public function show(JobPool $jobPool): JsonResponse
    {
        $jobPool->load(['company', 'skills', 'candidates']);

        return response()->success(
            new JobPoolResource($jobPool),
            'Job pool retrieved successfully'
        );
    }

    /**
     * Update the specified job pool in storage.
     *
     * @param JobPoolRequest $request
     * @param JobPool $jobPool
     * @return JsonResponse
     */
    public function update(JobPoolRequest $request, JobPool $jobPool): JsonResponse
    {
        $jobPool = $this->jobPoolService->update($jobPool, $request->validated());

        return response()->success(
            new JobPoolResource($jobPool),
            'Job pool updated successfully'
        );
    }

    /**
     * Remove the specified job pool from storage.
     *
     * @param JobPool $jobPool
     * @return JsonResponse
     */
    public function destroy(JobPool $jobPool): JsonResponse
    {
        $this->jobPoolService->delete($jobPool);

        return response()->success(null, 'Job pool deleted successfully');
    }

    /**
     * Get candidates in a job pool.
     *
     * @param Request $request
     * @param JobPool $jobPool
     * @return JsonResponse
     */
    public function getCandidates(Request $request, JobPool $jobPool): JsonResponse
    {
        $this->authorize('view', $jobPool);

        $filters = $request->only(['status', 'sort_by', 'sort_direction']);

        $candidates = $this->jobPoolService->getJobPoolCandidates($jobPool->id, $filters, $request->input('per_page', 15));

        return response()->paginatedSuccess(
            CandidateJobPoolResource::collection($candidates),
            'Job pool candidates retrieved successfully'
        );
    }

    /**
     * Add candidate to job pool.
     *
     * @param CandidateJobPoolRequest $request
     * @param JobPool $jobPool
     * @return JsonResponse
     */
    public function addCandidate(CandidateJobPoolRequest $request, JobPool $jobPool): JsonResponse
    {
        $this->authorize('update', $jobPool);

        $candidateJobPool = $this->jobPoolService->addCandidateToPool($jobPool->id, $request->validated());

        return response()->created(
            new CandidateJobPoolResource($candidateJobPool),
            'Candidate added to job pool successfully'
        );
    }

    /**
     * Update candidate in job pool.
     *
     * @param CandidateJobPoolRequest $request
     * @param JobPool $jobPool
     * @param int $candidateId
     * @return JsonResponse
     */
    public function updateCandidate(CandidateJobPoolRequest $request, JobPool $jobPool, int $candidateId): JsonResponse
    {
        $this->authorize('update', $jobPool);

        $candidateJobPool = $this->jobPoolService->updateCandidateInPool($jobPool->id, $candidateId, $request->validated());

        if (!$candidateJobPool) {
            return response()->notFound('Candidate not found in job pool');
        }

        return response()->success(
            new CandidateJobPoolResource($candidateJobPool),
            'Candidate in job pool updated successfully'
        );
    }

    /**
     * Remove candidate from job pool.
     *
     * @param JobPool $jobPool
     * @param int $candidateId
     * @return JsonResponse
     */
    public function removeCandidate(JobPool $jobPool, int $candidateId): JsonResponse
    {
        $this->authorize('update', $jobPool);

        $result = $this->jobPoolService->removeCandidateFromPool($jobPool->id, $candidateId);

        if (!$result) {
            return response()->notFound('Candidate not found in job pool');
        }

        return response()->success(null, 'Candidate removed from job pool successfully');
    }

    /**
     * Change candidate status in job pool.
     *
     * @param Request $request
     * @param JobPool $jobPool
     * @param int $candidateId
     * @return JsonResponse
     */
    public function changeCandidateStatus(Request $request, JobPool $jobPool, int $candidateId): JsonResponse
    {
        $this->authorize('update', $jobPool);

        $request->validate([
            'status' => 'required|string|in:pending,reviewing,shortlisted,interview,rejected,hired',
            'notes' => 'nullable|string',
        ]);

        $candidateJobPool = $this->jobPoolService->changeCandidateStatus(
            $jobPool->id,
            $candidateId,
            $request->status,
            $request->notes
        );

        if (!$candidateJobPool) {
            return response()->notFound('Candidate not found in job pool');
        }

        return response()->success(
            new CandidateJobPoolResource($candidateJobPool),
            'Candidate status changed successfully'
        );
    }

    /**
     * Get candidate status history in job pool.
     *
     * @param JobPool $jobPool
     * @param int $candidateId
     * @return JsonResponse
     */
    public function getCandidateStatusHistory(JobPool $jobPool, int $candidateId): JsonResponse
    {
        $this->authorize('view', $jobPool);

        $statusHistory = $this->jobPoolService->getCandidateStatusHistory($jobPool->id, $candidateId);

        if (!$statusHistory) {
            return response()->notFound('Candidate not found in job pool');
        }

        return response()->success(
            JobPoolStatusHistoryResource::collection($statusHistory),
            'Candidate status history retrieved successfully'
        );
    }

    /**
     * Toggle job pool active status.
     *
     * @param JobPool $jobPool
     * @return JsonResponse
     */
    public function toggleActive(JobPool $jobPool): JsonResponse
    {
        $this->authorize('update', $jobPool);

        $jobPool = $this->jobPoolService->toggleActive($jobPool);

        return response()->success(
            new JobPoolResource($jobPool),
            'Job pool active status toggled successfully'
        );
    }
}


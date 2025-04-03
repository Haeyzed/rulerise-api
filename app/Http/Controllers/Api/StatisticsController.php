<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StatisticsController extends Controller
{
    /**
     * @var StatisticsService
     */
    protected StatisticsService $statisticsService;

    /**
     * StatisticsController constructor.
     *
     * @param StatisticsService $statisticsService
     */
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Get dashboard statistics.
     *
     * @return JsonResponse
     */
    public function getDashboardStatistics(): JsonResponse
    {
        if (Gate::denies('view-dashboard-statistics')) {
            return response()->forbidden('You do not have permission to view dashboard statistics');
        }
        
        $statistics = $this->statisticsService->getDashboardStatistics();
        
        return response()->success($statistics, 'Dashboard statistics retrieved successfully');
    }

    /**
     * Get user statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-user-statistics')) {
            return response()->forbidden('You do not have permission to view user statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getUserStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'User statistics retrieved successfully');
    }

    /**
     * Get job statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getJobStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-job-statistics')) {
            return response()->forbidden('You do not have permission to view job statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getJobStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Job statistics retrieved successfully');
    }

    /**
     * Get company statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanyStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-company-statistics')) {
            return response()->forbidden('You do not have permission to view company statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getCompanyStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Company statistics retrieved successfully');
    }

    /**
     * Get candidate statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCandidateStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-candidate-statistics')) {
            return response()->forbidden('You do not have permission to view candidate statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getCandidateStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Candidate statistics retrieved successfully');
    }

    /**
     * Get application statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getApplicationStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-application-statistics')) {
            return response()->forbidden('You do not have permission to view application statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getApplicationStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Application statistics retrieved successfully');
    }

    /**
     * Get subscription statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSubscriptionStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-subscription-statistics')) {
            return response()->forbidden('You do not have permission to view subscription statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getSubscriptionStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Subscription statistics retrieved successfully');
    }

    /**
     * Get revenue statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRevenueStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-revenue-statistics')) {
            return response()->forbidden('You do not have permission to view revenue statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getRevenueStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Revenue statistics retrieved successfully');
    }

    /**
     * Get blog statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBlogStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-blog-statistics')) {
            return response()->forbidden('You do not have permission to view blog statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getBlogStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Blog statistics retrieved successfully');
    }

    /**
     * Get job pool statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getJobPoolStatistics(Request $request): JsonResponse
    {
        if (Gate::denies('view-job-pool-statistics')) {
            return response()->forbidden('You do not have permission to view job pool statistics');
        }
        
        $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
        
        $statistics = $this->statisticsService->getJobPoolStatistics($startDate, $endDate);
        
        return response()->success($statistics, 'Job pool statistics retrieved successfully');
    }
}


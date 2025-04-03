<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\CompanyReviewResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
  /**
   * The company service instance.
   *
   * @var CompanyService
   */
  protected $companyService;

  /**
   * Create a new controller instance.
   *
   * @param CompanyService $companyService
   * @return void
   */
  public function __construct(CompanyService $companyService)
  {
      $this->companyService = $companyService;
      // Remove the authorizeResource call - we'll handle authorization in each method
  }

  /**
   * Display a listing of the companies.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request): JsonResponse
  {
      // Authorization is handled via middleware in routes
      $filters = $request->only([
          'search', 'user_id', 'company_size_id', 'industry', 'country',
          'is_verified', 'is_featured', 'sort_by', 'sort_direction'
      ]);
      
      $companies = $this->companyService->list($filters, $request->input('per_page', 15));
      
      return response()->paginatedSuccess(
          CompanyResource::collection($companies),
          'Companies retrieved successfully'
      );
  }

  /**
   * Store a newly created company in storage.
   *
   * @param CompanyRequest $request
   * @return JsonResponse
   */
  public function store(CompanyRequest $request): JsonResponse
  {
      $this->authorize('create', Company::class);
      
      $company = $this->companyService->create($request->validated());
      
      return response()->created(
          new CompanyResource($company),
          'Company created successfully'
      );
  }

  /**
   * Display the specified company.
   *
   * @param Company $company
   * @return JsonResponse
   */
  public function show(Company $company): JsonResponse
  {
      $this->authorize('view', $company);
      
      // Increment views count
      $this->companyService->incrementViewsCount($company);
      
      // Load relationships
      $company->load(['user', 'companySize', 'jobs' => function ($query) {
          $query->active()->latest()->take(5);
      }]);
      
      return response()->success(
          new CompanyResource($company),
          'Company retrieved successfully'
      );
  }

  /**
   * Update the specified company in storage.
   *
   * @param CompanyRequest $request
   * @param Company $company
   * @return JsonResponse
   */
  public function update(CompanyRequest $request, Company $company): JsonResponse
  {
      $this->authorize('update', $company);
      
      $company = $this->companyService->update($company, $request->validated());
      
      return response()->success(
          new CompanyResource($company),
          'Company updated successfully'
      );
  }

  /**
   * Remove the specified company from storage.
   *
   * @param Company $company
   * @return JsonResponse
   */
  public function destroy(Company $company): JsonResponse
  {
      $this->authorize('delete', $company);
      
      $this->companyService->delete($company);
      
      return response()->success(null, 'Company deleted successfully');
  }

  /**
   * Get company jobs.
   *
   * @param Request $request
   * @param Company $company
   * @return JsonResponse
   */
  public function getJobs(Request $request, Company $company): JsonResponse
  {
      $this->authorize('view', $company);
      
      $filters = $request->only([
          'category_id', 'job_type_id', 'experience_level_id',
          'education_level_id', 'is_active', 'is_featured',
          'sort_by', 'sort_direction'
      ]);
      
      $jobs = $this->companyService->getCompanyJobs($company->id, $filters, $request->input('per_page', 15));
      
      return response()->paginatedSuccess(
          JobResource::collection($jobs),
          'Company jobs retrieved successfully'
      );
  }

  /**
   * Get company reviews.
   *
   * @param Request $request
   * @param Company $company
   * @return JsonResponse
   */
  public function getReviews(Request $request, Company $company): JsonResponse
  {
      $this->authorize('view', $company);
      
      $filters = $request->only(['rating', 'sort_by', 'sort_direction']);
      
      $reviews = $this->companyService->getCompanyReviews($company->id, $filters, $request->input('per_page', 15));
      
      return response()->paginatedSuccess(
          CompanyReviewResource::collection($reviews),
          'Company reviews retrieved successfully'
      );
  }

  /**
   * Verify company.
   *
   * @param Company $company
   * @return JsonResponse
   */
  public function verify(Company $company): JsonResponse
  {
      $this->authorize('verify', $company);
      
      $company = $this->companyService->verifyCompany($company);
      
      return response()->success(
          new CompanyResource($company),
          'Company verified successfully'
      );
  }

  /**
   * Toggle company featured status.
   *
   * @param Company $company
   * @return JsonResponse
   */
  public function toggleFeatured(Company $company): JsonResponse
  {
      $this->authorize('toggleFeatured', $company);
      
      $company = $this->companyService->toggleFeatured($company);
      
      return response()->success(
          new CompanyResource($company),
          'Company featured status toggled successfully'
      );
  }

  /**
   * Get company statistics.
   *
   * @param Company $company
   * @return JsonResponse
   */
  public function getStatistics(Company $company): JsonResponse
  {
      $this->authorize('viewStatistics', $company);
      
      $statistics = $this->companyService->getCompanyStatistics($company->id);
      
      return response()->success(
          $statistics,
          'Company statistics retrieved successfully'
      );
  }

  /**
   * Get company analytics.
   *
   * @param Request $request
   * @param Company $company
   * @return JsonResponse
   */
  public function getAnalytics(Request $request, Company $company): JsonResponse
  {
      $this->authorize('viewStatistics', $company);
      
      $startDate = $request->input('start_date') ? new \DateTime($request->input('start_date')) : null;
      $endDate = $request->input('end_date') ? new \DateTime($request->input('end_date')) : null;
      
      $analytics = $this->companyService->getCompanyAnalytics($company->id, $startDate, $endDate);
      
      return response()->success(
          $analytics,
          'Company analytics retrieved successfully'
      );
  }
}


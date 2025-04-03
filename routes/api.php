<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\WorldController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\JobPoolController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('register-employer', [AuthController::class, 'registerEmployer']);
    Route::post('register-candidate', [AuthController::class, 'registerCandidate']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:api');
    Route::put('profile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
    Route::post('password/change', [AuthController::class, 'changePassword'])->middleware('auth:api');
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware('auth:api');
    Route::post('account/deactivate', [AuthController::class, 'deactivateAccount'])->middleware('auth:api');
    Route::post('account/delete', [AuthController::class, 'deleteAccount'])->middleware('auth:api');
});

// World routes (countries, states, cities, currencies, timezones, languages)
//Route::prefix('world')->group(function () {
//    Route::get('countries', [WorldController::class, 'countries']);
//    Route::get('countries/{country}/states', [WorldController::class, 'states']);
//    Route::get('states/{state}/cities', [WorldController::class, 'cities']);
//    Route::get('currencies', [WorldController::class, 'currencies']);
//    Route::get('timezones', [WorldController::class, 'timezones']);
//    Route::get('languages', [WorldController::class, 'languages']);
//});

// Role and permission routes
Route::prefix('roles')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/', [RolePermissionController::class, 'getRoles']);
    Route::post('/', [RolePermissionController::class, 'createRole']);
    Route::get('/{role}', [RolePermissionController::class, 'getRole']);
    Route::put('/{role}', [RolePermissionController::class, 'updateRole']);
    Route::delete('/{role}', [RolePermissionController::class, 'deleteRole']);
    Route::post('/{role}/permissions', [RolePermissionController::class, 'assignPermissions']);
});

Route::prefix('permissions')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/', [RolePermissionController::class, 'getPermissions']);
    Route::post('/', [RolePermissionController::class, 'createPermission']);
    Route::get('/{permission}', [RolePermissionController::class, 'getPermission']);
    Route::put('/{permission}', [RolePermissionController::class, 'updatePermission']);
    Route::delete('/{permission}', [RolePermissionController::class, 'deletePermission']);
});

// Search routes
Route::prefix('search')->middleware('auth:api')->group(function () {
    Route::get('jobs', [SearchController::class, 'searchJobs']);
    Route::get('candidates', [SearchController::class, 'searchCandidates']);
    Route::get('companies', [SearchController::class, 'searchCompanies']);
    Route::get('blog', [SearchController::class, 'searchBlogPosts']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:users.view');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:users.edit');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete');
        Route::patch('/{user}/status', [UserController::class, 'updateStatus'])->middleware('permission:users.edit');
    });

    // Job routes
    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobController::class, 'index']);
        Route::get('/{job}', [JobController::class, 'show']);
        Route::post('/', [JobController::class, 'store'])->middleware('permission:jobs.create');
        Route::put('/{job}', [JobController::class, 'update'])->middleware('permission:jobs.edit');
        Route::delete('/{job}', [JobController::class, 'destroy'])->middleware('permission:jobs.delete');
        Route::patch('/{job}/status', [JobController::class, 'updateStatus'])->middleware('permission:jobs.edit');
        Route::patch('/{job}/featured', [JobController::class, 'toggleFeatured'])->middleware('permission:jobs.edit');
        Route::patch('/{job}/urgent', [JobController::class, 'toggleUrgent'])->middleware('permission:jobs.edit');
        Route::get('/{job}/similar', [JobController::class, 'getSimilarJobs']);
        Route::get('/{job}/statistics', [JobController::class, 'getStatistics'])->middleware('permission:jobs.view');
    });

    // Company routes
    Route::prefix('companies')->group(function () {
        Route::middleware(['permission:view companies'])->get('/', [CompanyController::class, 'index']);
        Route::get('/{company}', [CompanyController::class, 'show']);
        Route::middleware(['permission:create companies'])->post('/', [CompanyController::class, 'store'])->middleware('permission:companies.create');
        Route::middleware(['permission:update companies'])->put('/{company}', [CompanyController::class, 'update'])->middleware('permission:companies.edit');
        Route::middleware(['permission:delete companies'])->delete('/{company}', [CompanyController::class, 'destroy'])->middleware('permission:companies.delete');
        Route::patch('/{company}/verify', [CompanyController::class, 'verify'])->middleware('permission:companies.verify');
        Route::patch('/{company}/featured', [CompanyController::class, 'toggleFeatured'])->middleware('permission:companies.edit');
        Route::get('/{company}/jobs', [CompanyController::class, 'getJobs']);
        Route::get('/{company}/reviews', [CompanyController::class, 'getReviews']);
        Route::post('/{company}/reviews', [CompanyController::class, 'addReview']);
        Route::get('/{company}/statistics', [CompanyController::class, 'getStatistics'])->middleware('permission:companies.view');
        Route::get('/popular', [CompanyController::class, 'getPopularCompanies']);
        Route::get('/top-rated', [CompanyController::class, 'getTopRatedCompanies']);
    });

    // Candidate routes
    Route::prefix('candidates')->group(function () {
        Route::get('/', [CandidateController::class, 'index']);
        Route::get('/{candidateProfile}', [CandidateController::class, 'show']);
        Route::post('/', [CandidateController::class, 'store'])->middleware('permission:candidates.create');
        Route::put('/{candidateProfile}', [CandidateController::class, 'update'])->middleware('permission:candidates.edit');
        Route::delete('/{candidateProfile}', [CandidateController::class, 'destroy'])->middleware('permission:candidates.delete');
        Route::patch('/{candidateProfile}/featured', [CandidateController::class, 'toggleFeatured'])->middleware('permission:candidates.edit');
        Route::patch('/{candidateProfile}/availability', [CandidateController::class, 'toggleAvailability']);
        Route::patch('/{candidateProfile}/public', [CandidateController::class, 'togglePublic']);

        // Candidate skills
        Route::post('/{candidateProfile}/skills', [CandidateController::class, 'addSkill']);
        Route::put('/{candidateProfile}/skills/{skillId}', [CandidateController::class, 'updateSkill']);
        Route::delete('/{candidateProfile}/skills/{skillId}', [CandidateController::class, 'removeSkill']);

        // Candidate education
        Route::post('/{candidateProfile}/educations', [CandidateController::class, 'addEducation']);
        Route::put('/{candidateProfile}/educations/{educationId}', [CandidateController::class, 'updateEducation']);
        Route::delete('/{candidateProfile}/educations/{educationId}', [CandidateController::class, 'removeEducation']);

        // Candidate experience
        Route::post('/{candidateProfile}/experiences', [CandidateController::class, 'addExperience']);
        Route::put('/{candidateProfile}/experiences/{experienceId}', [CandidateController::class, 'updateExperience']);
        Route::delete('/{candidateProfile}/experiences/{experienceId}', [CandidateController::class, 'removeExperience']);

        // Candidate resumes
        Route::post('/{candidateProfile}/resumes', [CandidateController::class, 'uploadResume']);
        Route::delete('/{candidateProfile}/resumes/{resumeId}', [CandidateController::class, 'deleteResume']);
        Route::patch('/{candidateProfile}/resumes/{resumeId}/primary', [CandidateController::class, 'setResumePrimary']);

        Route::get('/{candidateProfile}/job-applications', [CandidateController::class, 'getJobApplications']);
        Route::get('/{candidateProfile}/statistics', [CandidateController::class, 'getStatistics']);
    });

    // Job application routes
    Route::prefix('job-applications')->group(function () {
        Route::get('/', [JobApplicationController::class, 'index'])->middleware('permission:job-applications.view');
        Route::get('/{jobApplication}', [JobApplicationController::class, 'show'])->middleware('permission:job-applications.view');
        Route::post('/', [JobApplicationController::class, 'store']);
        Route::put('/{jobApplication}', [JobApplicationController::class, 'update'])->middleware('permission:job-applications.edit');
        Route::delete('/{jobApplication}', [JobApplicationController::class, 'destroy'])->middleware('permission:job-applications.delete');
        Route::patch('/{jobApplication}/status', [JobApplicationController::class, 'updateStatus'])->middleware('permission:job-applications.edit');
        Route::patch('/{jobApplication}/viewed', [JobApplicationController::class, 'markAsViewed'])->middleware('permission:job-applications.edit');
        Route::post('/{jobApplication}/interviews', [JobApplicationController::class, 'scheduleInterview'])->middleware('permission:job-applications.edit');
        Route::get('/{jobApplication}/timeline', [JobApplicationController::class, 'getTimeline'])->middleware('permission:job-applications.view');
    });

    // Job pool routes
    Route::prefix('job-pools')->group(function () {
        Route::get('/', [JobPoolController::class, 'index'])->middleware('permission:job-pools.view');
        Route::get('/{jobPool}', [JobPoolController::class, 'show'])->middleware('permission:job-pools.view');
        Route::post('/', [JobPoolController::class, 'store'])->middleware('permission:job-pools.create');
        Route::put('/{jobPool}', [JobPoolController::class, 'update'])->middleware('permission:job-pools.edit');
        Route::delete('/{jobPool}', [JobPoolController::class, 'destroy'])->middleware('permission:job-pools.delete');

        Route::post('/{jobPool}/candidates', [JobPoolController::class, 'addCandidate'])->middleware('permission:job-pools.edit');
        Route::patch('/{jobPool}/candidates/{candidateJobPool}/status', [JobPoolController::class, 'updateCandidateStatus'])->middleware('permission:job-pools.edit');
        Route::delete('/{jobPool}/candidates/{candidateId}', [JobPoolController::class, 'removeCandidate'])->middleware('permission:job-pools.edit');

        Route::get('/{jobPool}/recommended-candidates', [JobPoolController::class, 'getRecommendedCandidates'])->middleware('permission:job-pools.view');
        Route::get('/{jobPool}/statistics', [JobPoolController::class, 'getStatistics'])->middleware('permission:job-pools.view');
    });

    // Blog post routes
    Route::prefix('blog-posts')->group(function () {
        Route::get('/', [BlogPostController::class, 'index']);
        Route::get('/{blogPost}', [BlogPostController::class, 'show']);
        Route::post('/', [BlogPostController::class, 'store'])->middleware('permission:blog-posts.create');
        Route::put('/{blogPost}', [BlogPostController::class, 'update'])->middleware('permission:blog-posts.edit');
        Route::delete('/{blogPost}', [BlogPostController::class, 'destroy'])->middleware('permission:blog-posts.delete');
        Route::patch('/{blogPost}/publish', [BlogPostController::class, 'publish'])->middleware('permission:blog-posts.edit');
        Route::patch('/{blogPost}/unpublish', [BlogPostController::class, 'unpublish'])->middleware('permission:blog-posts.edit');
        Route::patch('/{blogPost}/archive', [BlogPostController::class, 'archive'])->middleware('permission:blog-posts.edit');
        Route::get('/{blogPost}/related', [BlogPostController::class, 'getRelatedPosts']);
        Route::get('/popular', [BlogPostController::class, 'getPopularPosts']);
    });

    // Settings routes (admin only)
    Route::prefix('settings')->middleware('role:admin')->group(function () {
        Route::get('/', [SettingController::class, 'getAllSettings']);
        Route::get('/{key}', [SettingController::class, 'getSetting']);
        Route::post('/', [SettingController::class, 'setMultipleSettings']);
        Route::put('/{key}', [SettingController::class, 'setSetting']);
        Route::delete('/{key}', [SettingController::class, 'deleteSetting']);

        Route::get('/site', [SettingController::class, 'getSiteSettings']);
        Route::get('/seo', [SettingController::class, 'getSeoSettings']);
        Route::get('/email', [SettingController::class, 'getEmailSettings']);
        Route::get('/job', [SettingController::class, 'getJobSettings']);
        Route::get('/candidate', [SettingController::class, 'getCandidateSettings']);
        Route::get('/company', [SettingController::class, 'getCompanySettings']);
        Route::get('/blog', [SettingController::class, 'getBlogSettings']);
    });

    // Statistics routes (admin only)
    Route::prefix('statistics')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [StatisticsController::class, 'getDashboardStatistics']);
        Route::get('/users', [StatisticsController::class, 'getUserStatistics']);
        Route::get('/jobs', [StatisticsController::class, 'getJobStatistics']);
        Route::get('/applications', [StatisticsController::class, 'getApplicationStatistics']);
        Route::get('/companies', [StatisticsController::class, 'getCompanyStatistics']);
        Route::get('/candidates', [StatisticsController::class, 'getCandidateStatistics']);
        Route::get('/blog', [StatisticsController::class, 'getBlogStatistics']);
    });
});

// Public routes
Route::get('jobs/featured', [JobController::class, 'getFeaturedJobs']);
Route::get('jobs/latest', [JobController::class, 'getLatestJobs']);
Route::get('jobs/urgent', [JobController::class, 'getUrgentJobs']);
Route::get('jobs/trending', [JobController::class, 'getTrendingJobs']);

Route::get('companies/featured', [CompanyController::class, 'getFeaturedCompanies']);
Route::get('companies/popular', [CompanyController::class, 'getPopularCompanies']);
Route::get('companies/top-rated', [CompanyController::class, 'getTopRatedCompanies']);

Route::get('candidates/featured', [CandidateController::class, 'getFeaturedCandidates']);

Route::get('blog-posts/latest', [BlogPostController::class, 'getLatestPosts']);
Route::get('blog-posts/popular', [BlogPostController::class, 'getPopularPosts']);

// Categories, types, skills, etc.
Route::get('job-categories', [JobController::class, 'getJobCategories']);
Route::get('job-types', [JobController::class, 'getJobTypes']);
Route::get('experience-levels', [JobController::class, 'getExperienceLevels']);
Route::get('education-levels', [JobController::class, 'getEducationLevels']);
Route::get('skills', [JobController::class, 'getSkills']);
Route::get('company-sizes', [CompanyController::class, 'getCompanySizes']);
Route::get('blog-categories', [BlogPostController::class, 'getBlogCategories']);
Route::get('blog-tags', [BlogPostController::class, 'getBlogTags']);

// World routes (countries, states, cities, currencies, timezones, languages)
Route::prefix('world')->group(function () {
    // Countries
    Route::get('countries', [WorldController::class, 'getCountries']);
    Route::get('countries/{code}', [WorldController::class, 'getCountryByCode']);
    // States
    Route::get('countries/{countryCode}/states', [WorldController::class, 'getStates']);
    // Cities
    Route::get('countries/{countryCode}/states/{stateCode}/cities', [WorldController::class, 'getCities']);
    // Currencies
    Route::get('currencies', [WorldController::class, 'getCurrencies']);
    Route::get('currencies/{code}', [WorldController::class, 'getCurrencyByCode']);
    // Timezones
    Route::get('timezones', [WorldController::class, 'getTimezones']);
    Route::get('countries/{countryCode}/timezones', [WorldController::class, 'getTimezonesByCountry']);
    // Languages
    Route::get('languages', [WorldController::class, 'getLanguages']);
    Route::get('languages/{code}', [WorldController::class, 'getLanguageByCode']);
    // Phone codes
    Route::get('phone-codes', [WorldController::class, 'getPhoneCodes']);
    Route::get('countries/{countryCode}/phone-code', [WorldController::class, 'getPhoneCodeByCountry']);
    // Cache
    Route::post('clear-cache', [WorldController::class, 'clearCache'])->middleware('auth:api', 'role:admin');
});

<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobPool;
use App\Models\Setting;
use App\Models\User;
use App\Policies\BlogPostPolicy;
use App\Policies\CandidateProfilePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\JobApplicationPolicy;
use App\Policies\JobPolicy;
use App\Policies\JobPoolPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Job::class => JobPolicy::class,
        Company::class => CompanyPolicy::class,
        CandidateProfile::class => CandidateProfilePolicy::class,
        JobApplication::class => JobApplicationPolicy::class,
        BlogPost::class => BlogPostPolicy::class,
        JobPool::class => JobPoolPolicy::class,
        Setting::class => SettingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define a super-admin gate that grants all permissions
        Gate::before(function (User $user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });

        // Register permission-based gates for all permissions
        $this->registerPermissionGates();
    }

    /**
     * Register permission-based gates for all permissions.
     */
    protected function registerPermissionGates(): void
    {
        // User management gates
        Gate::define('viewAny-users', fn($user) => $user->hasPermissionTo('view users'));
        Gate::define('view-user', fn($user) => $user->hasPermissionTo('view users'));
        Gate::define('create-user', fn($user) => $user->hasPermissionTo('create users'));
        Gate::define('update-user', fn($user) => $user->hasPermissionTo('update users'));
        Gate::define('delete-user', fn($user) => $user->hasPermissionTo('delete users'));

        // Job management gates
        Gate::define('viewAny-jobs', fn($user) => $user->hasPermissionTo('view jobs'));
        Gate::define('view-job', fn($user) => $user->hasPermissionTo('view jobs'));
        Gate::define('create-job', fn($user) => $user->hasPermissionTo('create jobs'));
        Gate::define('update-job', fn($user) => $user->hasPermissionTo('update jobs'));
        Gate::define('delete-job', fn($user) => $user->hasPermissionTo('delete jobs'));

        // Company management gates
        Gate::define('viewAny-companies', fn($user) => $user->hasPermissionTo('view companies'));
        Gate::define('view-company', fn($user) => $user->hasPermissionTo('view companies'));
        Gate::define('create-company', fn($user) => $user->hasPermissionTo('create companies'));
        Gate::define('update-company', fn($user) => $user->hasPermissionTo('update companies'));
        Gate::define('delete-company', fn($user) => $user->hasPermissionTo('delete companies'));

        // Candidate profile management gates
        Gate::define('viewAny-candidate-profiles', fn($user) => $user->hasPermissionTo('view candidate profiles'));
        Gate::define('view-candidate-profile', fn($user) => $user->hasPermissionTo('view candidate profiles'));
        Gate::define('create-candidate-profile', fn($user) => $user->hasPermissionTo('create candidate profiles'));
        Gate::define('update-candidate-profile', fn($user) => $user->hasPermissionTo('update candidate profiles'));
        Gate::define('delete-candidate-profile', fn($user) => $user->hasPermissionTo('delete candidate profiles'));

        // Job application management gates
        Gate::define('viewAny-job-applications', fn($user) => $user->hasPermissionTo('view job applications'));
        Gate::define('view-job-application', fn($user) => $user->hasPermissionTo('view job applications'));
        Gate::define('create-job-application', fn($user) => $user->hasPermissionTo('create job applications'));
        Gate::define('update-job-application', fn($user) => $user->hasPermissionTo('update job applications'));
        Gate::define('delete-job-application', fn($user) => $user->hasPermissionTo('delete job applications'));

        // Blog post management gates
        Gate::define('viewAny-blog-posts', fn($user) => $user->hasPermissionTo('view blog posts'));
        Gate::define('view-blog-post', fn($user) => $user->hasPermissionTo('view blog posts'));
        Gate::define('create-blog-post', fn($user) => $user->hasPermissionTo('create blog posts'));
        Gate::define('update-blog-post', fn($user) => $user->hasPermissionTo('update blog posts'));
        Gate::define('delete-blog-post', fn($user) => $user->hasPermissionTo('delete blog posts'));

        // Job pool management gates
        Gate::define('viewAny-job-pools', fn($user) => $user->hasPermissionTo('view job pools'));
        Gate::define('view-job-pool', fn($user) => $user->hasPermissionTo('view job pools'));
        Gate::define('create-job-pool', fn($user) => $user->hasPermissionTo('create job pools'));
        Gate::define('update-job-pool', fn($user) => $user->hasPermissionTo('update job pools'));
        Gate::define('delete-job-pool', fn($user) => $user->hasPermissionTo('delete job pools'));

        // Role and permission management gates
        Gate::define('viewAny-roles', fn($user) => $user->hasPermissionTo('view roles'));
        Gate::define('view-role', fn($user) => $user->hasPermissionTo('view roles'));
        Gate::define('create-role', fn($user) => $user->hasPermissionTo('create roles'));
        Gate::define('update-role', fn($user) => $user->hasPermissionTo('update roles'));
        Gate::define('delete-role', fn($user) => $user->hasPermissionTo('delete roles'));

        Gate::define('viewAny-permissions', fn($user) => $user->hasPermissionTo('view permissions'));
        Gate::define('view-permission', fn($user) => $user->hasPermissionTo('view permissions'));
        Gate::define('create-permission', fn($user) => $user->hasPermissionTo('create permissions'));
        Gate::define('update-permission', fn($user) => $user->hasPermissionTo('update permissions'));
        Gate::define('delete-permission', fn($user) => $user->hasPermissionTo('delete permissions'));

        // Statistics gates
        Gate::define('view-statistics', fn($user) => $user->hasPermissionTo('view statistics'));

        // Settings gates
        Gate::define('view-settings', fn($user) => $user->hasPermissionTo('view settings'));
        Gate::define('update-settings', fn($user) => $user->hasPermissionTo('update settings'));

        // Register gates for statistics
        Gate::define('view-dashboard-statistics', function (User $user) {
            return $user->hasPermissionTo('view dashboard statistics');
        });
        
        Gate::define('view-user-statistics', function (User $user) {
            return $user->hasPermissionTo('view user statistics');
        });
        
        Gate::define('view-job-statistics', function (User $user) {
            return $user->hasPermissionTo('view job statistics');
        });
        
        Gate::define('view-company-statistics', function (User $user) {
            return $user->hasPermissionTo('view company statistics');
        });
        
        Gate::define('view-candidate-statistics', function (User $user) {
            return $user->hasPermissionTo('view candidate statistics');
        });
        
        Gate::define('view-application-statistics', function (User $user) {
            return $user->hasPermissionTo('view application statistics');
        });
        
        Gate::define('view-subscription-statistics', function (User $user) {
            return $user->hasPermissionTo('view subscription statistics');
        });
        
        Gate::define('view-revenue-statistics', function (User $user) {
            return $user->hasPermissionTo('view revenue statistics');
        });
        
        Gate::define('view-blog-statistics', function (User $user) {
            return $user->hasPermissionTo('view blog statistics');
        });
        
        Gate::define('view-job-pool-statistics', function (User $user) {
            return $user->hasPermissionTo('view job pool statistics');
        });

        // Register gates for role and permission management
        Gate::define('manage-roles', function (User $user) {
            return $user->hasPermissionTo('manage roles');
        });
        
        Gate::define('manage-permissions', function (User $user) {
            return $user->hasPermissionTo('manage permissions');
        });
        
        Gate::define('assign-roles', function (User $user) {
            return $user->hasPermissionTo('assign roles');
        });
        
        Gate::define('assign-permissions', function (User $user) {
            return $user->hasPermissionTo('assign permissions');
        });

        // Register gates for settings
        Gate::define('manage-settings', function (User $user) {
            return $user->hasPermissionTo('manage settings');
        });
        
        Gate::define('view-settings', function (User $user) {
            return $user->hasPermissionTo('view settings');
        });
    }
}


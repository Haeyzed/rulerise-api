<?php

namespace Database\Seeders;

use App\Enums\SupportLevelEnum;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => Str::slug('Free'),
                'description' => 'Basic plan for small businesses',
                'price' => 0,
                'currency' => 'USD',
                'duration_days' => 0, // Unlimited
                'job_posts_limit' => 3,
                'featured_jobs_limit' => 0,
                'resume_views_limit' => 10,
                'job_alerts' => false,
                'candidate_search' => false,
                'resume_access' => false,
                'company_profile' => true,
                'support_level' => SupportLevelEnum::BASIC,
                'is_active' => true,
                'is_featured' => false,
                'features' => [
                    'Basic company profile',
                    'Limited job postings',
                    'Standard job listing',
                    'Email support'
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Starter',
                'slug' => Str::slug('Starter'),
                'description' => 'Perfect for growing businesses',
                'price' => 49.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'job_posts_limit' => 10,
                'featured_jobs_limit' => 2,
                'resume_views_limit' => 50,
                'job_alerts' => true,
                'candidate_search' => true,
                'resume_access' => false,
                'company_profile' => true,
                'support_level' => SupportLevelEnum::STANDARD,
                'is_active' => true,
                'is_featured' => false,
                'features' => [
                    'Enhanced company profile',
                    'Up to 10 job postings',
                    '2 featured job listings',
                    'Basic candidate search',
                    'Job alerts',
                    'Standard support'
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional',
                'slug' => Str::slug('Professional'),
                'description' => 'Ideal for medium-sized businesses',
                'price' => 99.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'job_posts_limit' => 25,
                'featured_jobs_limit' => 5,
                'resume_views_limit' => 200,
                'job_alerts' => true,
                'candidate_search' => true,
                'resume_access' => true,
                'company_profile' => true,
                'support_level' => SupportLevelEnum::PRIORITY,
                'is_active' => true,
                'is_featured' => true,
                'features' => [
                    'Premium company profile',
                    'Up to 25 job postings',
                    '5 featured job listings',
                    'Advanced candidate search',
                    'Resume database access',
                    'Job alerts',
                    'Priority support',
                    'Analytics dashboard'
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'slug' => Str::slug('Enterprise'),
                'description' => 'Complete solution for large organizations',
                'price' => 199.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'job_posts_limit' => 100,
                'featured_jobs_limit' => 20,
                'resume_views_limit' => 500,
                'job_alerts' => true,
                'candidate_search' => true,
                'resume_access' => true,
                'company_profile' => true,
                'support_level' => SupportLevelEnum::DEDICATED,
                'is_active' => true,
                'is_featured' => true,
                'features' => [
                    'Premium company profile with branding',
                    'Up to 100 job postings',
                    '20 featured job listings',
                    'Advanced candidate search with filters',
                    'Full resume database access',
                    'Custom job alerts',
                    'Dedicated account manager',
                    'Advanced analytics and reporting',
                    'API access',
                    'Branded career page'
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Annual Enterprise',
                'slug' => Str::slug('Annual Enterprise'),
                'description' => 'Annual plan with maximum benefits',
                'price' => 1999.99,
                'currency' => 'USD',
                'duration_days' => 365,
                'job_posts_limit' => 1000,
                'featured_jobs_limit' => 200,
                'resume_views_limit' => 5000,
                'job_alerts' => true,
                'candidate_search' => true,
                'resume_access' => true,
                'company_profile' => true,
                'support_level' => SupportLevelEnum::DEDICATED,
                'is_active' => true,
                'is_featured' => true,
                'features' => [
                    'Premium company profile with custom branding',
                    'Up to 1000 job postings annually',
                    '200 featured job listings',
                    'Advanced candidate search with custom filters',
                    'Full resume database access with bulk actions',
                    'Custom job alerts and notifications',
                    'Dedicated account manager and support team',
                    'Comprehensive analytics and custom reporting',
                    'Full API access and integration support',
                    'Custom branded career page',
                    'Recruitment marketing tools',
                    'Talent pool management'
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
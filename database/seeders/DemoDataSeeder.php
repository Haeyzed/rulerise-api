<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\ExperienceLevel;
use App\Models\CandidateProfile;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Skill;
use App\Models\CandidateSkill;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\JobApplication;
use App\Models\EducationLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'role' => UserRoleEnum::ADMIN->value,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $adminUser->assignRole(UserRoleEnum::ADMIN->value);

        // Create employer users and companies
        $companySizes = CompanySize::all();
        
        for ($i = 1; $i <= 5; $i++) {
            $employerUser = User::create([
                'first_name' => "Employer $i",
                'last_name' => "Employer $i",
                'email' => "employer$i@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => UserRoleEnum::EMPLOYER->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $employerUser->assignRole(UserRoleEnum::EMPLOYER->value);
            
            $companySize = $companySizes->random();
            
            Company::create([
                'user_id' => $employerUser->id,
                'name' => "Company $i",
                'slug' => Str::slug("Company $i"),
                'company_size_id' => $companySize->id,
                'website' => "https://company$i.example.com",
                'founded_year' => rand(1980, 2020),
                'description' => "This is a sample description for Company $i. We are a leading provider of innovative solutions in our industry.",
                'logo' => "/images/companies/company$i.png",
                'banner' => "/images/companies/banner$i.jpg",
                'address' => "123 Business St, Suite $i",
                'phone' => "+1 (555) 123-456$i",
                'email' => "contact@company$i.example.com",
                'facebook_url' => "https://facebook.com/company$i",
                'twitter_url' => "https://twitter.com/company$i",
                'linkedin_url' => "https://linkedin.com/company/company$i",
                'instagram_url' => "https://instagram.com/company$i",
                'is_featured' => $i <= 2, // First 2 companies are featured
                'is_active' => true,
                'is_verified' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Create candidate users and profiles
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $educationLevels = EducationLevel::all();
        
        for ($i = 1; $i <= 10; $i++) {
            $candidateUser = User::create([
                'first_name' => "Candidate $i",
                'last_name' => "Candidate $i",
                'email' => "candidate$i@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => UserRoleEnum::CANDIDATE->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $candidateUser->assignRole(UserRoleEnum::CANDIDATE->value);
            
            $country = $countries->random();
            $state = $states->where('country_id', $country->id)->first() ?? $states->random();
            $city = $cities->where('state_id', $state->id)->first() ?? $cities->random();
            $educationLevel = $educationLevels->random();
            
            CandidateProfile::create([
                'user_id' => $candidateUser->id,
                'headline' => "Experienced Professional $i",
                'summary' => "I am an experienced professional with expertise in various domains. I have worked on multiple projects and have a strong background in my field.",
                'current_salary' => rand(50000, 150000),
                'expected_salary' => rand(60000, 180000),
                'experience_years' => rand(1, 15),
                'education_level_id' => $educationLevel->id,
                'date_of_birth' => now()->subYears(rand(22, 55))->format('Y-m-d'),
                'gender' => ['male', 'female', 'other'][rand(0, 2)],
                'phone' => "+1 (555) 987-654$i",
                'website' => "https://candidate$i.example.com",
                'address' => "456 Residential Ave, Apt $i",
                'city_id' => $city->id,
                'state_id' => $state->id,
                'country_id' => $country->id,
                'postal_code' => "1000$i",
                'is_public' => true,
                'is_available' => $i % 3 != 0, // 2/3 of candidates are available
                'avatar' => "/images/candidates/avatar$i.jpg",
                'cover_image' => "/images/candidates/cover$i.jpg",
                'video_introduction' => $i % 5 == 0 ? "https://example.com/videos/intro$i.mp4" : null,
                'facebook_url' => "https://facebook.com/candidate$i",
                'twitter_url' => "https://twitter.com/candidate$i",
                'linkedin_url' => "https://linkedin.com/in/candidate$i",
                'github_url' => "https://github.com/candidate$i",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Add skills to candidates
            $skills = Skill::inRandomOrder()->take(rand(3, 8))->get();
            foreach ($skills as $skill) {
                CandidateSkill::create([
                    'candidate_id' => $candidateUser->id,
                    'skill_id' => $skill->id,
                    'proficiency_level' => ['beginner', 'intermediate', 'advanced', 'expert'][rand(0, 3)],
                    // 'years_of_experience' => rand(1, 10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Add education to candidates
            for ($e = 0; $e < rand(1, 3); $e++) {
                CandidateEducation::create([
                    'candidate_id' => $candidateUser->id,
                    'education_level_id' => $educationLevels->random()->id,
                    'institution' => "University " . chr(65 + $e),
                    'field_of_study' => ['Computer Science', 'Business Administration', 'Engineering', 'Marketing', 'Finance'][rand(0, 4)],
                    // 'degree' => ['Bachelor', 'Master', 'PhD', 'Associate'][rand(0, 3)],
                    'start_date' => now()->subYears(rand(5, 15))->format('Y-m-d'),
                    'end_date' => rand(0, 1) ? now()->subYears(rand(1, 4))->format('Y-m-d') : null,
                    'is_current' => rand(0, 1) ? true : false,
                    'description' => "Studied with focus on specialized areas and participated in various academic projects.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Add experience to candidates
            for ($exp = 0; $exp < rand(1, 4); $exp++) {
                CandidateExperience::create([
                    'candidate_id' => $candidateUser->id,
                    'company_name' => "Previous Employer " . chr(65 + $exp),
                    'job_title' => ['Software Developer', 'Project Manager', 'Marketing Specialist', 'Financial Analyst', 'HR Manager'][rand(0, 4)],
                    'start_date' => now()->subYears(rand(1, 10))->subMonths(rand(0, 11))->format('Y-m-d'),
                    'end_date' => rand(0, 1) ? now()->subMonths(rand(1, 11))->format('Y-m-d') : null,
                    'is_current' => rand(0, 1) ? true : false,
                    'location' => $city->name . ', ' . $state->name,
                    'description' => "Responsible for key projects and initiatives that delivered significant business value.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Create jobs
        $companies = Company::all();
        $jobCategories = JobCategory::all();
        $jobTypes = JobType::all();
        $experienceLevels = ExperienceLevel::all();
        
        for ($i = 1; $i <= 30; $i++) {
            $company = $companies->random();
            $category = $jobCategories->random();
            $type = $jobTypes->random();
            $expLevel = $experienceLevels->random();
            $country = $countries->random();
            $state = $states->where('country_id', $country->id)->first() ?? $states->random();
            $city = $cities->where('state_id', $state->id)->first() ?? $cities->random();
            
            $job = Job::create([
                'company_id' => $company->id,
                'job_category_id' => $category->id,
                'job_type_id' => $type->id,
                'experience_level_id' => $expLevel->id,
                'title' => "Job Position $i",
                'slug' => Str::slug("Job Position $i"),
                'description' => "This is a detailed description for Job Position $i. We are looking for a talented individual to join our team.",
                'requirements' => "- Bachelor's degree in related field\n- At least {$expLevel->years_min} years of experience\n- Strong communication skills\n- Problem-solving abilities",
                'benefits' => "- Competitive salary\n- Health insurance\n- Flexible working hours\n- Professional development opportunities",
                'salary_min' => rand(30000, 80000),
                'salary_max' => rand(90000, 200000),
                'salary_period' => ['hourly', 'daily', 'weekly', 'monthly', 'yearly'][rand(0, 4)],
                'location' => $city->name . ', ' . $state->name,
                'address' => "123 Workplace St, Building $i",
                'country_id' => $country->id,
                'state_id' => $state->id,
                'city_id' => $city->id,
                'is_remote' => $i % 3 == 0,
                'application_deadline' => now()->addDays(rand(7, 30))->format('Y-m-d'),
                'is_featured' => $i % 5 == 0,
                'is_active' => true,
                'vacancies' => rand(1, 5),
                'views_count' => rand(50, 500),
                'applications_count' => 0,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
            
            // Add required skills to jobs
            $jobSkills = Skill::inRandomOrder()->take(rand(3, 6))->get();
            $job->skills()->attach($jobSkills->pluck('id')->toArray());
            
            // Create job applications
            $candidates = CandidateProfile::inRandomOrder()->take(rand(0, 5))->get();
            foreach ($candidates as $candidate) {
                $applicationDate = now()->subDays(rand(1, 20));
                $application = JobApplication::create([
                    'job_id' => $job->id,
                    'candidate_profile_id' => $candidate->id,
                    'cover_letter' => "I am very interested in the {$job->title} position at {$company->name}. My skills and experience align well with your requirements.",
                    'status' => ['pending', 'reviewed', 'shortlisted', 'rejected', 'hired'][rand(0, 4)],
                    'applied_at' => $applicationDate,
                    'created_at' => $applicationDate,
                    'updated_at' => $applicationDate,
                ]);
                
                // Update applications count
                $job->applications_count = $job->applications_count + 1;
                $job->save();
            }
        }
        
        // Create blog posts
        $blogCategories = BlogCategory::all();
        $blogTags = BlogTag::all();
        
        for ($i = 1; $i <= 15; $i++) {
            $publishedDate = now()->subDays(rand(1, 60));
            $post = BlogPost::create([
                'user_id' => $adminUser->id,
                'title' => "Blog Post $i: Career Tips and Advice",
                'slug' => Str::slug("Blog Post $i Career Tips and Advice"),
                'content' => "<h2>Introduction</h2><p>This is a sample blog post with career tips and advice. It contains useful information for job seekers and professionals.</p><h2>Main Points</h2><p>Here are some key points to consider in your career journey:</p><ul><li>Always keep learning and developing new skills</li><li>Network with professionals in your industry</li><li>Maintain an updated resume and online profile</li><li>Prepare thoroughly for interviews</li></ul><h2>Conclusion</h2><p>Following these tips can help you advance in your career and find better opportunities.</p>",
                'excerpt' => "Discover essential career tips and advice to help you succeed in your professional journey.",
                'featured_image' => "/images/blog/post$i.jpg",
                'is_published' => true,
                'published_at' => $publishedDate,
                'meta_title' => "Career Tips and Advice | Blog Post $i",
                'meta_description' => "Read our latest blog post about career tips and professional advice.",
                'meta_keywords' => "career tips, professional advice, job search, career development",
                'views_count' => rand(100, 1000),
                'created_at' => $publishedDate,
                'updated_at' => $publishedDate,
            ]);
            
            // Attach random categories and tags
            $post->categories()->attach($blogCategories->random(rand(1, 3))->pluck('id')->toArray());
            $post->tags()->attach($blogTags->random(rand(3, 8))->pluck('id')->toArray());
        }
    }
}


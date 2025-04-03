<?php

namespace Database\Seeders;

use App\Models\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Career Growth', 'slug' => 'career-growth', 'is_active' => true],
            ['name' => 'Job Search', 'slug' => 'job-search', 'is_active' => true],
            ['name' => 'Resume Tips', 'slug' => 'resume-tips', 'is_active' => true],
            ['name' => 'Interview Preparation', 'slug' => 'interview-preparation', 'is_active' => true],
            ['name' => 'Networking', 'slug' => 'networking', 'is_active' => true],
            ['name' => 'Professional Skills', 'slug' => 'professional-skills', 'is_active' => true],
            ['name' => 'Industry Insights', 'slug' => 'industry-insights', 'is_active' => true],
            ['name' => 'Workplace Trends', 'slug' => 'workplace-trends', 'is_active' => true],
            ['name' => 'Remote Work', 'slug' => 'remote-work', 'is_active' => true],
            ['name' => 'Work-Life Balance', 'slug' => 'work-life-balance', 'is_active' => true],
            ['name' => 'Leadership', 'slug' => 'leadership', 'is_active' => true],
            ['name' => 'Management', 'slug' => 'management', 'is_active' => true],
            ['name' => 'Entrepreneurship', 'slug' => 'entrepreneurship', 'is_active' => true],
            ['name' => 'Freelancing', 'slug' => 'freelancing', 'is_active' => true],
            ['name' => 'Technology', 'slug' => 'technology', 'is_active' => true],
            ['name' => 'Healthcare', 'slug' => 'healthcare', 'is_active' => true],
            ['name' => 'Finance', 'slug' => 'finance', 'is_active' => true],
            ['name' => 'Marketing', 'slug' => 'marketing', 'is_active' => true],
            ['name' => 'Education', 'slug' => 'education', 'is_active' => true],
            ['name' => 'Engineering', 'slug' => 'engineering', 'is_active' => true],
            ['name' => 'Diversity', 'slug' => 'diversity', 'is_active' => true],
            ['name' => 'Inclusion', 'slug' => 'inclusion', 'is_active' => true],
            ['name' => 'Employee Benefits', 'slug' => 'employee-benefits', 'is_active' => true],
            ['name' => 'Salary Negotiation', 'slug' => 'salary-negotiation', 'is_active' => true],
            ['name' => 'Career Change', 'slug' => 'career-change', 'is_active' => true],
            ['name' => 'Mentorship', 'slug' => 'mentorship', 'is_active' => true],
            ['name' => 'Professional Development', 'slug' => 'professional-development', 'is_active' => true],
            ['name' => 'Soft Skills', 'slug' => 'soft-skills', 'is_active' => true],
            ['name' => 'Technical Skills', 'slug' => 'technical-skills', 'is_active' => true],
            ['name' => 'Workplace Culture', 'slug' => 'workplace-culture', 'is_active' => true],
        ];

        foreach ($tags as $tag) {
            $tag['created_at'] = now();
            $tag['updated_at'] = now();
            BlogTag::create($tag);
        }
    }
}


<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Career Advice',
                'slug' => 'career-advice',
                'description' => 'Tips and guidance for career development and job searching',
                'icon' => 'lightbulb',
                'color' => '#3498db',
                'is_active' => true,
                'order' => 1,
                'meta_title' => 'Career Advice | Job Portal Blog',
                'meta_description' => 'Get expert career advice, job search tips, and professional development guidance.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Industry Trends',
                'slug' => 'industry-trends',
                'description' => 'Latest developments and trends across various industries',
                'icon' => 'trending_up',
                'color' => '#2ecc71',
                'is_active' => true,
                'order' => 2,
                'meta_title' => 'Industry Trends | Job Portal Blog',
                'meta_description' => 'Stay updated with the latest industry trends, market insights, and sector developments.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Workplace Culture',
                'slug' => 'workplace-culture',
                'description' => 'Insights on company culture, work environment, and employee engagement',
                'icon' => 'groups',
                'color' => '#e74c3c',
                'is_active' => true,
                'order' => 3,
                'meta_title' => 'Workplace Culture | Job Portal Blog',
                'meta_description' => 'Explore articles about workplace culture, employee engagement, and building positive work environments.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Interview Tips',
                'slug' => 'interview-tips',
                'description' => 'Preparation strategies and best practices for job interviews',
                'icon' => 'record_voice_over',
                'color' => '#f39c12',
                'is_active' => true,
                'order' => 4,
                'meta_title' => 'Interview Tips | Job Portal Blog',
                'meta_description' => 'Master the art of interviewing with our expert tips, preparation strategies, and common questions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Resume Building',
                'slug' => 'resume-building',
                'description' => 'Guidance on creating effective resumes and cover letters',
                'icon' => 'description',
                'color' => '#9b59b6',
                'is_active' => true,
                'order' => 5,
                'meta_title' => 'Resume Building | Job Portal Blog',
                'meta_description' => 'Learn how to create standout resumes and cover letters that get you noticed by employers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Job Market Analysis',
                'slug' => 'job-market-analysis',
                'description' => 'Data-driven insights on job market conditions and opportunities',
                'icon' => 'analytics',
                'color' => '#34495e',
                'is_active' => true,
                'order' => 6,
                'meta_title' => 'Job Market Analysis | Job Portal Blog',
                'meta_description' => 'Get data-driven insights on job market trends, salary information, and employment opportunities.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Remote Work',
                'slug' => 'remote-work',
                'description' => 'Strategies and tools for effective remote work and virtual collaboration',
                'icon' => 'home',
                'color' => '#16a085',
                'is_active' => true,
                'order' => 7,
                'meta_title' => 'Remote Work | Job Portal Blog',
                'meta_description' => 'Discover tips for successful remote work, virtual collaboration, and maintaining work-life balance.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Development',
                'slug' => 'professional-development',
                'description' => 'Resources for skill enhancement and continuous learning',
                'icon' => 'school',
                'color' => '#27ae60',
                'is_active' => true,
                'order' => 8,
                'meta_title' => 'Professional Development | Job Portal Blog',
                'meta_description' => 'Find resources for continuous learning, skill development, and career advancement.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Entrepreneurship',
                'slug' => 'entrepreneurship',
                'description' => 'Insights for startups, small business owners, and entrepreneurs',
                'icon' => 'business',
                'color' => '#7f8c8d',
                'is_active' => true,
                'order' => 9,
                'meta_title' => 'Entrepreneurship | Job Portal Blog',
                'meta_description' => 'Get advice for startups, small business owners, and aspiring entrepreneurs.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diversity & Inclusion',
                'slug' => 'diversity-inclusion',
                'description' => 'Promoting diversity, equity, and inclusion in the workplace',
                'icon' => 'diversity_3',
                'color' => '#95a5a6',
                'is_active' => true,
                'order' => 10,
                'meta_title' => 'Diversity & Inclusion | Job Portal Blog',
                'meta_description' => 'Learn about promoting diversity, equity, and inclusion in the modern workplace.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Work-Life Balance',
                'slug' => 'work-life-balance',
                'description' => 'Strategies for maintaining healthy boundaries between work and personal life',
                'icon' => 'balance',
                'color' => '#e67e22',
                'is_active' => true,
                'order' => 11,
                'meta_title' => 'Work-Life Balance | Job Portal Blog',
                'meta_description' => 'Discover strategies for maintaining a healthy work-life balance and preventing burnout.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leadership',
                'slug' => 'leadership',
                'description' => 'Developing leadership skills and effective management practices',
                'icon' => 'psychology',
                'color' => '#3498db',
                'is_active' => true,
                'order' => 12,
                'meta_title' => 'Leadership | Job Portal Blog',
                'meta_description' => 'Learn about effective leadership strategies, team management, and organizational development.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }
    }
}


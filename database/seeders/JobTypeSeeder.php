<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobTypes = [
            [
                'name' => 'Full-time',
                'slug' => 'full-time',
                'description' => 'Standard 40-hour work week with benefits',
                'icon' => 'work',
                'color' => '#3498db',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Part-time',
                'slug' => 'part-time',
                'description' => 'Less than 40 hours per week',
                'icon' => 'schedule',
                'color' => '#2ecc71',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Contract',
                'slug' => 'contract',
                'description' => 'Fixed-term employment agreement',
                'icon' => 'description',
                'color' => '#e74c3c',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Temporary',
                'slug' => 'temporary',
                'description' => 'Short-term position with defined end date',
                'icon' => 'timer',
                'color' => '#f39c12',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Internship',
                'slug' => 'internship',
                'description' => 'Learning opportunity for students or recent graduates',
                'icon' => 'school',
                'color' => '#9b59b6',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Freelance',
                'slug' => 'freelance',
                'description' => 'Independent contractor work on project basis',
                'icon' => 'person',
                'color' => '#34495e',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Remote',
                'slug' => 'remote',
                'description' => 'Work from anywhere position',
                'icon' => 'home',
                'color' => '#16a085',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hybrid',
                'slug' => 'hybrid',
                'description' => 'Combination of remote and in-office work',
                'icon' => 'compare_arrows',
                'color' => '#27ae60',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Volunteer',
                'slug' => 'volunteer',
                'description' => 'Unpaid position for charitable or community work',
                'icon' => 'volunteer_activism',
                'color' => '#7f8c8d',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Apprenticeship',
                'slug' => 'apprenticeship',
                'description' => 'On-the-job training with educational components',
                'icon' => 'build',
                'color' => '#95a5a6',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($jobTypes as $type) {
            JobType::create($type);
        }
    }
}


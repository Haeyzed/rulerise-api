<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationLevels = [
            [
                'name' => 'High School',
                'slug' => 'high-school',
                'description' => 'High school diploma or equivalent',
                'icon' => 'school',
                'color' => '#3498db',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Associate Degree',
                'slug' => 'associate-degree',
                'description' => 'Two-year degree from community college or technical school',
                'icon' => 'menu_book',
                'color' => '#2ecc71',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bachelor\'s Degree',
                'slug' => 'bachelors-degree',
                'description' => 'Four-year undergraduate degree',
                'icon' => 'history_edu',
                'color' => '#e74c3c',
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Master\'s Degree',
                'slug' => 'masters-degree',
                'description' => 'Graduate-level academic degree',
                'icon' => 'workspace_premium',
                'color' => '#f39c12',
                'is_active' => true,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Doctorate',
                'slug' => 'doctorate',
                'description' => 'Highest academic degree (PhD, MD, JD, etc.)',
                'icon' => 'psychology',
                'color' => '#9b59b6',
                'is_active' => true,
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vocational Training',
                'slug' => 'vocational-training',
                'description' => 'Specialized training for specific trades or careers',
                'icon' => 'construction',
                'color' => '#34495e',
                'is_active' => true,
                'order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Certification',
                'slug' => 'certification',
                'description' => 'Professional certification in specific field',
                'icon' => 'verified',
                'color' => '#16a085',
                'is_active' => true,
                'order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Some College',
                'slug' => 'some-college',
                'description' => 'Partial college education without degree completion',
                'icon' => 'auto_stories',
                'color' => '#7f8c8d',
                'is_active' => true,
                'order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Post-Doctoral',
                'slug' => 'post-doctoral',
                'description' => 'Advanced research and academic experience after doctorate',
                'icon' => 'biotech',
                'color' => '#27ae60',
                'is_active' => true,
                'order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($educationLevels as $level) {
            EducationLevel::create($level);
        }
    }
}


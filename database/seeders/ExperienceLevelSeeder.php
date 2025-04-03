<?php

namespace Database\Seeders;

use App\Models\ExperienceLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExperienceLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experienceLevels = [
            [
                'name' => 'Entry Level',
                'slug' => 'entry-level',
                'description' => '0-2 years of experience',
                'years_min' => 0,
                'years_max' => 2,
                'icon' => 'star_border',
                'color' => '#3498db',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Junior',
                'slug' => 'junior',
                'description' => '2-3 years of experience',
                'years_min' => 2,
                'years_max' => 3,
                'icon' => 'star_half',
                'color' => '#2ecc71',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mid-Level',
                'slug' => 'mid-level',
                'description' => '3-5 years of experience',
                'years_min' => 3,
                'years_max' => 5,
                'icon' => 'star',
                'color' => '#e74c3c',
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Senior',
                'slug' => 'senior',
                'description' => '5-8 years of experience',
                'years_min' => 5,
                'years_max' => 8,
                'icon' => 'stars',
                'color' => '#f39c12',
                'is_active' => true,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lead',
                'slug' => 'lead',
                'description' => '8-10 years of experience',
                'years_min' => 8,
                'years_max' => 10,
                'icon' => 'auto_awesome',
                'color' => '#9b59b6',
                'is_active' => true,
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => '10+ years of experience with management responsibilities',
                'years_min' => 10,
                'years_max' => 15,
                'icon' => 'supervisor_account',
                'color' => '#34495e',
                'is_active' => true,
                'order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Director',
                'slug' => 'director',
                'description' => '15+ years of experience with strategic leadership',
                'years_min' => 15,
                'years_max' => 20,
                'icon' => 'groups',
                'color' => '#16a085',
                'is_active' => true,
                'order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Executive',
                'slug' => 'executive',
                'description' => '20+ years of experience with executive leadership',
                'years_min' => 20,
                'years_max' => null,
                'icon' => 'emoji_events',
                'color' => '#27ae60',
                'is_active' => true,
                'order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($experienceLevels as $level) {
            ExperienceLevel::create($level);
        }
    }
}


<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WorldSeeder::class,
            RolesAndPermissionsSeeder::class,
            CompanySizeSeeder::class,
            SubscriptionPlanSeeder::class,
            JobCategorySeeder::class,
            JobTypeSeeder::class,
            ExperienceLevelSeeder::class,
            EducationLevelSeeder::class,
            SkillSeeder::class,
            BlogCategorySeeder::class,
            BlogTagSeeder::class,
            PageSeeder::class,
            SettingSeeder::class,
            // DemoDataSeeder::class, // Optional - for demo data
        ]);
    }
}


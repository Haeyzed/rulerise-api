<?php

namespace Database\Seeders;

use App\Models\CompanySize;
use Illuminate\Database\Seeder;

class CompanySizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companySizes = [
            [
                'name' => '1-10 employees',
                'description' => 'Micro company or startup',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '11-50 employees',
                'description' => 'Small company',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '51-200 employees',
                'description' => 'Medium-sized company',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '201-500 employees',
                'description' => 'Large company',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '501-1,000 employees',
                'description' => 'Very large company',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1,001-5,000 employees',
                'description' => 'Enterprise',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '5,001-10,000 employees',
                'description' => 'Large enterprise',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '10,001+ employees',
                'description' => 'Multinational corporation',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($companySizes as $size) {
            CompanySize::create($size);
        }
    }
}

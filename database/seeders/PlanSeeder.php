<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'regular_monthly',
                'name' => 'Regular — Monthly',
                'price_dzd' => 3000,
                'duration_months' => 1,
                'is_student' => false,
            ],
            [
                'slug' => 'regular_yearly',
                'name' => 'Regular — Yearly',
                'price_dzd' => 30000,
                'duration_months' => 12,
                'is_student' => false,
            ],
            [
                'slug' => 'student_monthly',
                'name' => 'Student — Monthly',
                'price_dzd' => 1500,
                'duration_months' => 1,
                'is_student' => true,
            ],
            [
                'slug' => 'student_yearly',
                'name' => 'Student — Yearly',
                'price_dzd' => 7500,
                'duration_months' => 12,
                'is_student' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}

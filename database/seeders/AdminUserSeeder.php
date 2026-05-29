<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sketchlib.com'],
            [
                'name' => 'SketchLib Admin',
                'password' => Hash::make('sketchlib-admin'),
                'is_admin' => true,
            ],
        );
    }
}

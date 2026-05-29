<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class BetaUserSeeder extends Seeder
{
    public function run(): void
    {
        $betaUsers = [
            'Bouchra', 'Aïda', 'Maroua', 'Rania', 'Amira',
            'Hinda', 'Imene', 'Alycia', 'Sarah', 'Walid',
            'Yasmine', 'Amine',
        ];

        $plan = Plan::where('slug', 'regular_monthly')->first();

        if (! $plan) {
            return;
        }

        foreach ($betaUsers as $name) {
            $email = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $name)).'@beta.sketchlib.com';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => 'beta2024!',
                    'is_beta' => true,
                ]
            );

            Subscription::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'beta'],
                [
                    'plan_id' => $plan->id,
                    'status' => 'beta',
                    'starts_at' => now(),
                    'ends_at' => null,
                ]
            );
        }

        $sarah = User::where('email', 'sarah@beta.sketchlib.com')->first();
        if ($sarah) {
            $sarah->update(['is_admin' => true]);
        }
    }
}

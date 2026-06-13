<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Minimalist',
            'Modern',
            'Classic',
            'Industrial',
            'Scandinavian',
            'Rustic',
            'Contemporary',
            'Luxury',
            'Compact',
            'Outdoor',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}

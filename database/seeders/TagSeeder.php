<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Sofa', 'slug' => 'sofa', 'group' => 'type'],
            ['name' => 'Chair', 'slug' => 'chair', 'group' => 'type'],
            ['name' => 'Table', 'slug' => 'table', 'group' => 'type'],
            ['name' => 'Bed', 'slug' => 'bed', 'group' => 'type'],
            ['name' => 'Desk', 'slug' => 'desk', 'group' => 'type'],
            ['name' => 'Cabinet', 'slug' => 'cabinet', 'group' => 'type'],
            ['name' => 'Shelf', 'slug' => 'shelf', 'group' => 'type'],
            ['name' => 'Lamp', 'slug' => 'lamp', 'group' => 'type'],

            ['name' => 'Wood', 'slug' => 'wood', 'group' => 'material'],
            ['name' => 'Fabric', 'slug' => 'fabric', 'group' => 'material'],
            ['name' => 'Metal', 'slug' => 'metal', 'group' => 'material'],
            ['name' => 'Marble', 'slug' => 'marble', 'group' => 'material'],
            ['name' => 'Glass', 'slug' => 'glass', 'group' => 'material'],
            ['name' => 'Leather', 'slug' => 'leather', 'group' => 'material'],
            ['name' => 'Plastic', 'slug' => 'plastic', 'group' => 'material'],

            ['name' => 'Minimalist', 'slug' => 'minimalist', 'group' => 'style'],
            ['name' => 'Modern', 'slug' => 'modern', 'group' => 'style'],
            ['name' => 'Classic', 'slug' => 'classic', 'group' => 'style'],
            ['name' => 'Industrial', 'slug' => 'industrial', 'group' => 'style'],
            ['name' => 'Scandinavian', 'slug' => 'scandinavian', 'group' => 'style'],
            ['name' => 'Rustic', 'slug' => 'rustic', 'group' => 'style'],
            ['name' => 'Contemporary', 'slug' => 'contemporary', 'group' => 'style'],
            ['name' => 'Luxury', 'slug' => 'luxury', 'group' => 'style'],
            ['name' => 'Compact', 'slug' => 'compact', 'group' => 'style'],
            ['name' => 'Outdoor', 'slug' => 'outdoor', 'group' => 'style'],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}

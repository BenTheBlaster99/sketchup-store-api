<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryPack;
use App\Models\SketchupModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Living Room',
                'description' => 'Sofas, coffee tables, TV units',
                'pack_price' => 5000,
                'models' => [
                    ['name' => 'Modern Sofa 3-Seater', 'published' => true],
                    ['name' => 'Oak Coffee Table', 'published' => true],
                    ['name' => 'Minimal TV Stand', 'published' => false],
                ],
            ],
            [
                'name' => 'Kitchen',
                'description' => 'Cabinets, islands, appliances',
                'pack_price' => 4500,
                'models' => [
                    ['name' => 'L-Shaped Kitchen Island', 'published' => true],
                    ['name' => 'Wall Cabinet Set', 'published' => true],
                ],
            ],
            [
                'name' => 'Bedroom',
                'description' => 'Beds, wardrobes, nightstands',
                'pack_price' => 4000,
                'models' => [
                    ['name' => 'Queen Platform Bed', 'published' => true],
                ],
            ],
        ];

        foreach ($categories as $index => $data) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );

            CategoryPack::updateOrCreate(
                ['category_id' => $category->id],
                ['price_dzd' => $data['pack_price'], 'is_active' => true]
            );

            foreach ($data['models'] as $modelIndex => $modelData) {
                $slug = Str::slug($modelData['name']);

                SketchupModel::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $category->id,
                        'name' => $modelData['name'],
                        'file_key' => "models/{$category->slug}/{$slug}.skp",
                        'thumbnail_key' => "thumbnails/{$category->slug}/{$slug}.jpg",
                        'file_size_bytes' => 2_500_000,
                        'sketchup_version_min' => 2022,
                        'is_published' => $modelData['published'],
                        'sort_order' => $modelIndex + 1,
                    ]
                );
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryPack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
            'pack_price' => 'nullable|integer|min:0',
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        if (isset($data['pack_price'])) {
            CategoryPack::create([
                'category_id' => $category->id,
                'price_dzd' => $data['pack_price'],
            ]);
        }

        return response()->json($category->load('pack'), 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => 'string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'pack_price' => 'nullable|integer|min:0',
        ]);

        $packPrice = $data['pack_price'] ?? null;
        unset($data['pack_price']);

        $category->update($data);

        if (array_key_exists('pack_price', $request->all())) {
            CategoryPack::updateOrCreate(
                ['category_id' => $category->id],
                ['price_dzd' => $packPrice, 'is_active' => true]
            );
        }

        return response()->json($category->fresh()->load('pack'));
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

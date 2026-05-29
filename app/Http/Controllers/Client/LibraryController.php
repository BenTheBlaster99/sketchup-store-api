<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SketchupModel;
use App\Services\AccessService;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function __construct(
        private AccessService $access,
        private StorageService $storage
    ) {}

    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->with('pack')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                $cat->model_count = $cat->models()
                    ->where('is_published', true)
                    ->count();
                $cat->cover_image_url = $cat->cover_image_key
                    ? $this->storage->presignedThumbnailUrl($cat->cover_image_key)
                    : null;

                return $cat;
            });

        return response()->json($categories);
    }

    public function categoryModels(Request $request, string $slug): JsonResponse
    {
        $user = $request->user();
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $categoryIds = $this->access->getAccessibleCategoryIds($user);
        $hasFullAccess = empty($categoryIds);

        if (! $hasFullAccess && ! in_array($category->id, $categoryIds)) {
            return response()->json(['message' => 'No access to this category'], 403);
        }

        $models = SketchupModel::where('category_id', $category->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($model) {
                $model->thumbnail_url = $model->thumbnail_key
                    ? $this->storage->presignedThumbnailUrl($model->thumbnail_key)
                    : null;

                return $model;
            });

        return response()->json([
            'category' => $category,
            'models' => $models,
        ]);
    }
}

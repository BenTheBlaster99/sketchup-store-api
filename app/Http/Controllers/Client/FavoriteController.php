<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ModelFavorite;
use App\Models\SketchupModel;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(private StorageService $storage) {}

    public function toggle(Request $request, SketchupModel $model): JsonResponse
    {
        if (! $model->is_published || $model->review_status !== 'approved') {
            abort(404);
        }

        $user = $request->user();

        $existing = ModelFavorite::where('user_id', $user->id)
            ->where('sketchup_model_id', $model->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $model->decrement('likes_count');
            $favorited = false;
        } else {
            ModelFavorite::create([
                'user_id' => $user->id,
                'sketchup_model_id' => $model->id,
            ]);
            $model->increment('likes_count');
            $favorited = true;
        }

        return response()->json([
            'favorited' => $favorited,
            'likes_count' => $model->fresh()->likes_count,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $models = SketchupModel::whereHas('favorites', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })
            ->where('is_published', true)
            ->where('review_status', 'approved')
            ->with(['tags', 'creator:id,name,display_name'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($model) {
                $model->thumbnail_url = $model->thumbnail_key
                    ? $this->storage->presignedThumbnailUrl($model->thumbnail_key)
                    : null;
                $model->is_favorited = true;

                return $model;
            });

        return response()->json($models);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SketchupModel;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModelAdminController extends Controller
{
    public function __construct(private StorageService $storage) {}

    public function index(): JsonResponse
    {
        $models = SketchupModel::with(['category', 'creator:id,name,display_name', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($models);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'file_key' => 'required|string',
            'thumbnail_key' => 'nullable|string',
            'file_size_bytes' => 'required|integer',
            'sketchup_version_min' => 'required|integer|min:2020|max:2030',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        $model = SketchupModel::create([
            ...collect($data)->except('tag_ids')->all(),
            'slug' => Str::slug($data['name']).'-'.Str::random(6),
            'is_published' => false,
            'review_status' => 'approved',
        ]);

        if (! empty($data['tag_ids'])) {
            $model->tags()->sync($data['tag_ids']);
        }

        return response()->json($model->load(['category', 'tags']), 201);
    }

    public function update(Request $request, SketchupModel $model): JsonResponse
    {
        $data = $request->validate([
            'name' => 'string|max:200',
            'category_id' => 'exists:categories,id',
            'sketchup_version_min' => 'integer|min:2020|max:2030',
            'is_published' => 'boolean',
            'review_status' => 'in:approved,pending_review,rejected',
            'rejection_note' => 'nullable|string',
            'sort_order' => 'integer',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        $model->update(collect($data)->except('tag_ids')->all());

        if (array_key_exists('tag_ids', $data)) {
            $model->tags()->sync($data['tag_ids'] ?? []);
        }

        return response()->json($model->load(['category', 'tags']));
    }

    public function destroy(SketchupModel $model): JsonResponse
    {
        $this->storage->delete($model->file_key);
        if ($model->thumbnail_key) {
            $this->storage->delete($model->thumbnail_key);
        }
        $model->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

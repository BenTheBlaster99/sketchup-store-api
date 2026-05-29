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
        $models = SketchupModel::with('category')
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
        ]);

        $model = SketchupModel::create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::random(6),
            'is_published' => false,
        ]);

        return response()->json($model, 201);
    }

    public function update(Request $request, SketchupModel $model): JsonResponse
    {
        $data = $request->validate([
            'name' => 'string|max:200',
            'category_id' => 'exists:categories,id',
            'sketchup_version_min' => 'integer|min:2020|max:2030',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $model->update($data);

        return response()->json($model);
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

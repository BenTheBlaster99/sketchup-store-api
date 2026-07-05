<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SketchupModel;
use App\Models\Tag;
use App\Services\AutoTagService;
use App\Services\CreatorEarningsService;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreatorPortalController extends Controller
{
    public function __construct(
        private StorageService $storage,
        private AutoTagService $autoTag,
        private CreatorEarningsService $earnings,
    ) {}

    public function models(Request $request): JsonResponse
    {
        $models = SketchupModel::where('creator_id', $request->user()->id)
            ->with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($model) {
                $model->thumbnail_url = $model->thumbnail_key
                    ? $this->storage->presignedThumbnailUrl($model->thumbnail_key)
                    : null;

                return $model->makeVisible('file_key');
            });

        return response()->json($models);
    }

    public function presign(Request $request): JsonResponse
    {
        $request->validate([
            'file_name' => 'required|string',
            'file_type' => 'required|string',
            'thumbnail_name' => 'required|string',
            'thumbnail_type' => 'required|string',
            'category_slug' => 'required|string',
        ]);

        $category = Category::where('slug', $request->category_slug)
            ->where('is_active', true)
            ->firstOrFail();

        $fileName = Str::slug(pathinfo($request->file_name, PATHINFO_FILENAME));
        $prefix = 'models/'.$category->slug;
        $fileKey = $prefix.'/'.Str::uuid().'-'.$fileName.'.skp';
        $thumbKey = 'thumbnails/'.$category->slug.'/'.Str::uuid().'.jpg';

        return response()->json([
            'file_upload_url' => $this->storage->presignedUploadUrl($fileKey, $request->file_type),
            'file_key' => $fileKey,
            'thumb_upload_url' => $this->storage->presignedUploadUrl($thumbKey, $request->thumbnail_type),
            'thumb_key' => $thumbKey,
        ]);
    }

    public function autotag(Request $request): JsonResponse
    {
        $data = $request->validate([
            'thumbnail_key' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
        ]);

        if (empty($data['thumbnail_key']) && empty($data['thumbnail_url'])) {
            return response()->json(['message' => 'thumbnail_key or thumbnail_url is required.'], 422);
        }

        $thumbnailUrl = $data['thumbnail_url'] ?? null;

        if ($thumbnailUrl && ! filter_var($thumbnailUrl, FILTER_VALIDATE_URL)) {
            $thumbnailUrl = $this->storage->presignedThumbnailUrl($thumbnailUrl);
        }

        $thumbnailUrl ??= $this->storage->presignedThumbnailUrl($data['thumbnail_key']);

        $tagIds = $this->autoTag->suggestTags($thumbnailUrl);
        $tags = Tag::whereIn('id', $tagIds)
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        return response()->json([
            'suggested_tag_ids' => $tagIds,
            'suggested_tags' => $tags,
        ]);
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
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(6),
            'file_key' => $data['file_key'],
            'thumbnail_key' => $data['thumbnail_key'] ?? null,
            'file_size_bytes' => $data['file_size_bytes'],
            'sketchup_version_min' => $data['sketchup_version_min'],
            'creator_id' => $request->user()->id,
            'is_published' => false,
            'review_status' => 'pending_review',
        ]);

        if (! empty($data['tag_ids'])) {
            $model->tags()->sync($data['tag_ids']);
        }

        return response()->json($model->load(['category', 'tags'])->makeVisible('file_key'), 201);
    }

    public function earnings(Request $request): JsonResponse
    {
        return response()->json(
            $this->earnings->forCreator($request->user())
        );
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'display_name' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'paypal_email' => 'nullable|email|max:255',
        ]);

        $request->user()->update($data);

        return response()->json($request->user()->fresh());
    }
}

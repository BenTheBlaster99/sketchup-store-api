<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\AutoTagService;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function __construct(
        private StorageService $storage,
        private AutoTagService $autoTag,
    ) {}

    public function presign(Request $request): JsonResponse
    {
        $request->validate([
            'file_name' => 'required|string',
            'file_type' => 'required|string',
            'thumbnail_name' => 'required|string',
            'thumbnail_type' => 'required|string',
            'category_slug' => 'required|string',
        ]);

        $prefix = 'models/'.$request->category_slug;
        $fileKey = $prefix.'/'.Str::uuid().'-'.Str::slug(pathinfo($request->file_name, PATHINFO_FILENAME)).'.skp';
        $thumbKey = 'thumbnails/'.$request->category_slug.'/'.Str::uuid().'.jpg';

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
}

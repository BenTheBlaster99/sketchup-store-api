<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function __construct(private StorageService $storage) {}

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
}

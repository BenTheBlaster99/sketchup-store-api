<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\SketchupModel;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function __construct(private StorageService $storage) {}

    public function download(Request $request, SketchupModel $model): JsonResponse
    {
        if (! $model->is_published) {
            abort(404);
        }

        $user = $request->user();

        if (! $user->canAccess($model)) {
            return response()->json(['message' => 'No access to this model'], 403);
        }

        Download::create([
            'user_id' => $user->id,
            'sketchup_model_id' => $model->id,
            'ip_address' => $request->ip(),
            'delivered_at' => now(),
        ]);

        $url = $this->storage->presignedDownloadUrl($model->file_key);

        return response()->json(['download_url' => $url]);
    }
}

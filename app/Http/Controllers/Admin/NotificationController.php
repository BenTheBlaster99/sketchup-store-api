<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewModelsDropped;
use App\Models\SketchupModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function notifyNewModels(Request $request): JsonResponse
    {
        $request->validate([
            'model_ids' => 'required|array',
            'model_ids.*' => 'exists:sketchup_models,id',
        ]);

        $models = SketchupModel::with('category')
            ->whereIn('id', $request->model_ids)
            ->get();

        $users = User::whereHas('subscriptions', function ($q) {
            $q->whereIn('status', ['active', 'beta'])
                ->where(function ($q2) {
                    $q2->whereNull('ends_at')->orWhere('ends_at', '>', now());
                });
        })->get();

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new NewModelsDropped($user, $models));
        }

        return response()->json([
            'message' => 'Notification queued for '.$users->count().' subscribers',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CreatorApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorApplicationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->is_creator) {
            return response()->json(['message' => 'You are already a creator.'], 422);
        }

        $existing = CreatorApplication::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You already have a '.$existing->status.' application.',
                'status' => $existing->status,
            ], 422);
        }

        $data = $request->validate([
            'bio' => 'required|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'paypal_email' => 'required|email|max:255',
        ]);

        $application = CreatorApplication::create([
            ...$data,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $user->update(['creator_status' => 'pending']);

        return response()->json($application, 201);
    }

    public function status(Request $request): JsonResponse
    {
        $application = CreatorApplication::where('user_id', $request->user()->id)
            ->latest()
            ->first();

        return response()->json($application);
    }
}

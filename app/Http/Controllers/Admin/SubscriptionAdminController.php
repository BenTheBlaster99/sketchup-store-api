<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'pending');

        $subscriptions = Subscription::with(['user', 'plan'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($subscriptions);
    }

    public function activate(Request $request, Subscription $subscription): JsonResponse
    {
        $request->validate(['admin_note' => 'nullable|string']);

        if ($subscription->status !== 'pending') {
            return response()->json(['message' => 'Only pending subscriptions can be activated'], 422);
        }

        $subscription->load('plan');
        $subscription->activate();
        $subscription->update(['admin_note' => $request->admin_note]);

        return response()->json($subscription->fresh()->load(['user', 'plan']));
    }

    public function reject(Request $request, Subscription $subscription): JsonResponse
    {
        $request->validate(['admin_note' => 'nullable|string']);

        $subscription->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        return response()->json($subscription->fresh());
    }
}

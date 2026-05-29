<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();

        $active = $user->activeSubscription()->with('plan')->first();
        if ($active) {
            return response()->json($active);
        }

        $pending = $user->subscriptions()
            ->where('status', 'pending')
            ->with('plan')
            ->latest()
            ->first();

        return response()->json($pending);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payer_name' => 'required|string|max:100',
            'payment_ref' => 'required|string|max:100',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        $existing = $request->user()
            ->subscriptions()
            ->whereIn('status', ['pending', 'active', 'beta'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You already have a pending or active subscription.',
                'existing_status' => $existing->status,
                'subscription' => $existing->load('plan'),
            ], 422);
        }

        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'payer_name' => $data['payer_name'],
            'payment_ref' => $data['payment_ref'],
        ]);

        return response()->json($subscription->load('plan'), 201);
    }
}

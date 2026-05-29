<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CategoryPack;
use App\Models\PackPurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackController extends Controller
{
    public function purchased(Request $request): JsonResponse
    {
        $purchases = $request->user()
            ->packPurchases()
            ->where('status', 'active')
            ->with('category')
            ->get();

        return response()->json($purchases);
    }

    public function store(Request $request, int $categoryId): JsonResponse
    {
        $data = $request->validate([
            'payer_name' => 'required|string|max:100',
            'payment_ref' => 'required|string|max:100',
        ]);

        $pack = CategoryPack::where('category_id', $categoryId)
            ->where('is_active', true)
            ->firstOrFail();

        $existing = PackPurchase::where('user_id', $request->user()->id)
            ->where('category_id', $categoryId)
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Already purchased or pending',
                'existing_status' => $existing->status,
                'purchase' => $existing->load('category'),
            ], 422);
        }

        $purchase = PackPurchase::create([
            'user_id' => $request->user()->id,
            'category_id' => $categoryId,
            'status' => 'pending',
            'price_paid_dzd' => $pack->price_dzd,
            'payer_name' => $data['payer_name'],
            'payment_ref' => $data['payment_ref'],
        ]);

        return response()->json($purchase->load('category'), 201);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackPurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackPurchaseAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'pending');

        $purchases = PackPurchase::with(['user', 'category'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($purchases);
    }

    public function activate(Request $request, PackPurchase $purchase): JsonResponse
    {
        $request->validate(['admin_note' => 'nullable|string']);

        $purchase->update([
            'status' => 'active',
            'admin_note' => $request->admin_note,
        ]);

        return response()->json($purchase->fresh()->load(['user', 'category']));
    }

    public function reject(Request $request, PackPurchase $purchase): JsonResponse
    {
        $request->validate(['admin_note' => 'nullable|string']);

        $purchase->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        return response()->json($purchase->fresh());
    }
}

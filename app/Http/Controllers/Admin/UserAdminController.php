<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with('activeSubscription.plan')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'is_student' => 'boolean',
            'is_beta' => 'boolean',
            'is_admin' => 'boolean',
            'hardware_id' => 'nullable|string',
        ]);

        $user->update($data);

        return response()->json($user->fresh());
    }
}

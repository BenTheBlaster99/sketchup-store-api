<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json($result, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if ($result === null) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (isset($result['error']) && $result['error'] === 'device_mismatch') {
            return response()->json([
                'message' => 'This account is linked to a different device. Contact support to reset.',
            ], 403);
        }

        return response()->json($result);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('activeSubscription.plan');

        $packCategoryIds = $user->packPurchases()
            ->where('status', 'active')
            ->pluck('category_id');

        return response()->json([
            'user' => $user,
            'pack_category_ids' => $packCategoryIds,
        ]);
    }
}

<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ModelAdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PackPurchaseAdminController;
use App\Http\Controllers\Admin\SubscriptionAdminController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\DownloadController;
use App\Http\Controllers\Client\FavoriteController;
use App\Http\Controllers\Client\LibraryController;
use App\Http\Controllers\Client\PackController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\WaitlistController;
use App\Models\Plan;
use App\Models\Tag;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::get('categories', [LibraryController::class, 'categories']);
Route::get('plans', fn () => Plan::where('is_active', true)->get());
Route::get('tags', fn () => Tag::orderBy('name')->get(['id', 'name', 'slug']));
Route::post('waitlist', [WaitlistController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('categories/{slug}/models', [LibraryController::class, 'categoryModels']);
    Route::get('models/favorites', [FavoriteController::class, 'index']);
    Route::post('models/{model}/favorite', [FavoriteController::class, 'toggle']);
    Route::post('models/{model}/download', [DownloadController::class, 'download']);

    Route::get('subscriptions/current', [SubscriptionController::class, 'current']);
    Route::post('subscriptions', [SubscriptionController::class, 'store']);

    Route::get('packs/purchased', [PackController::class, 'purchased']);
    Route::post('packs/{categoryId}', [PackController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('upload/presign', [UploadController::class, 'presign']);

    Route::get('models', [ModelAdminController::class, 'index']);
    Route::post('models', [ModelAdminController::class, 'store']);
    Route::put('models/{model}', [ModelAdminController::class, 'update']);
    Route::delete('models/{model}', [ModelAdminController::class, 'destroy']);

    Route::post('categories', [AdminCategoryController::class, 'store']);
    Route::put('categories/{category}', [AdminCategoryController::class, 'update']);
    Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy']);

    Route::get('users', [UserAdminController::class, 'index']);
    Route::put('users/{user}', [UserAdminController::class, 'update']);

    Route::get('subscriptions', [SubscriptionAdminController::class, 'index']);
    Route::put('subscriptions/{subscription}/activate', [SubscriptionAdminController::class, 'activate']);
    Route::put('subscriptions/{subscription}/reject', [SubscriptionAdminController::class, 'reject']);

    Route::get('pack-purchases', [PackPurchaseAdminController::class, 'index']);
    Route::put('pack-purchases/{purchase}/activate', [PackPurchaseAdminController::class, 'activate']);
    Route::put('pack-purchases/{purchase}/reject', [PackPurchaseAdminController::class, 'reject']);

    Route::post('notify/new-models', [NotificationController::class, 'notifyNewModels']);
    Route::post('waitlist/notify', [WaitlistController::class, 'notifyAll']);
});

<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Api\Admin\SocialMediaController as AdminSocialMediaController;
use App\Http\Controllers\Api\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Api\Admin\AccountImageController;
use App\Http\Controllers\Api\Admin\AccountCredentialController;
use App\Http\Controllers\Api\Public\PublicAccountController;
use App\Http\Controllers\Api\Public\PublicBannerController;
use App\Http\Controllers\Api\Public\PublicSocialMediaController;

// PUBLIC
Route::prefix('public')->group(function () {
    Route::get('accounts', [PublicAccountController::class, 'index']);
    Route::get('accounts/{id}', [PublicAccountController::class, 'show']);
    Route::get('accounts/{id}/whatsapp', [PublicAccountController::class, 'whatsappLink']);

    Route::get('banners', [PublicBannerController::class, 'index']);

    Route::get('social-medias', [PublicSocialMediaController::class, 'index']);
});

// ADMIN
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout']);

        Route::get('accounts', [AdminAccountController::class, 'index']);
        Route::post('accounts', [AdminAccountController::class, 'store']);
        Route::get('accounts/{id}', [AdminAccountController::class, 'show']);
        Route::put('accounts/{id}', [AdminAccountController::class, 'update']);
        Route::delete('accounts/{id}', [AdminAccountController::class, 'destroy']);

        Route::post('accounts/{id}/approve', [AdminAccountController::class, 'approve']);
        Route::post('accounts/{id}/sold', [AdminAccountController::class, 'sold']);

        Route::post('accounts/{id}/images', [AccountImageController::class, 'store']);
        Route::delete('accounts/images/{imageId}', [AccountImageController::class, 'destroy']);

        Route::get('accounts/{id}/credential', [AccountCredentialController::class, 'show']);
        Route::put('accounts/{id}/credential', [AccountCredentialController::class, 'upsert']);
        Route::delete('accounts/{id}/credential', [AccountCredentialController::class, 'destroy']);

        Route::get('banners', [AdminBannerController::class, 'index']);
        Route::post('banners', [AdminBannerController::class, 'store']);
        Route::put('banners/{id}', [AdminBannerController::class, 'update']);
        Route::post('banners/{id}/image', [AdminBannerController::class, 'updateImage']);
        Route::delete('banners/{id}', [AdminBannerController::class, 'destroy']);

        Route::get('social-medias', [AdminSocialMediaController::class, 'index']);
        Route::post('social-medias', [AdminSocialMediaController::class, 'store']);
        Route::get('social-medias/{id}', [AdminSocialMediaController::class, 'show']);
        Route::put('social-medias/{id}', [AdminSocialMediaController::class, 'update']);
        Route::delete('social-medias/{id}', [AdminSocialMediaController::class, 'destroy']);
    });
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Admin\AdminCampaignController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Regular user can list campaigns & donate
    Route::get('campaigns', [CampaignController::class,'index']);
    Route::get('campaigns/{campaign}', [CampaignController::class,'show']);
    Route::post('campaigns/{campaign}/checkout',[DonationController::class,'checkout']);
    Route::post('donations/confirm',[DonationController::class,'confirmPayment']);

    // Admin area
    Route::middleware('admin')->prefix('admin')->group(function(){
        Route::apiResource('campaigns', AdminCampaignController::class);
        Route::get('users', [AdminUserController::class,'index']);
    });
});




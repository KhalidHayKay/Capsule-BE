<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;

Route::prefix('/waitlist')->group(function () {
    Route::get('', [WaitlistController::class, 'index']);
    Route::post('/new', [WaitlistController::class, 'store']);
    Route::get('/{waitlist}', [WaitlistController::class, 'show']);
});

Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/social-login', [AuthController::class, 'socialLogin']);
    Route::post('/logout/{all?}', [AuthController::class, 'logout'])
        ->where('all', 'all');

    Route::prefix('/email')->group(function () {
        Route::post('/verify', [EmailVerificationController::class, 'verify']);
        Route::post('/send-code', [EmailVerificationController::class, 'code']);
    });

    Route::prefix('/password')->group(function () {
        Route::post('/forgot', [PasswordController::class, 'token']);
        Route::post('/reset', [PasswordController::class, 'reset']);
    });
});

Route::middleware(['auth:sanctum', 'verified'])->get('/check-email', function () {
    return response()->json(['message' => 'Email is verified']);
});

<?php

use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmailVerificationController;

Route::prefix('waitlist')->group(function () {
    Route::get('/', [WaitlistController::class, 'index']);
    Route::post('/new', [WaitlistController::class, 'store']);
    Route::get('/{waitlist}', [WaitlistController::class, 'show']);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/social-login', [AuthController::class, 'socialLogin']);
    Route::post('/logout/{all?}', [AuthController::class, 'logout'])
        ->where('all', 'all');

    Route::prefix('email')->group(function () {
        Route::post('/verify', [EmailVerificationController::class, 'verify']);
        Route::post('/send-code', [EmailVerificationController::class, 'resend']);
    });

    // Route::prefix('password')->group(function () {
    //     Route::post('request-code', [AuthController::class, 'resetPassword']);
    //     Route::post('reset', [AuthController::class, 'resetPassword']);
    //     Route::post('forgot', [AuthController::class, 'forgotPassword']);
    // });
});

Route::middleware(['auth:sanctum', 'verified'])->get('/check-email', function () {
    return "Email verified";
});

<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WaitlistController;
use Illuminate\Support\Facades\Route;

Route::controller(WaitlistController::class)->group(function () {
    Route::get('/waitlist/{waitlist}', 'show');
    Route::post('/waitlist/new', 'store');
    Route::get('/waitlist', 'index');
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('logout/{all?}', [AuthController::class, 'logout', true]);
    Route::get('me', [AuthController::class, 'me']);
})->middleware('auth:sanctum');

// Route::group(['middleware' => 'auth:sanctum'], function () {
//     Route::get('me', [AuthController::class, 'me']);
// })->middleware('auth:sanctum');

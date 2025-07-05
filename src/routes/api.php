<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WaitlistController;
use Illuminate\Support\Facades\Route;

Route::controller(WaitlistController::class)->group(function () {
    Route::get('/waitlist/{waitlist}', 'show');
    Route::post('/waitlist/new', 'store');
    Route::get('/waitlist', 'index');
});

Route::get('/users/all', [UserController::class, 'index']);

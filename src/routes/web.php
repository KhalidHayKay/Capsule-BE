<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::prefix('docs')->group(function () {
    Scramble::registerUiRoute('api');
    Scramble::registerJsonSpecificationRoute('api.json');
});

if (app()->environment('local')) {
    Route::get('/preview/mail/{view}', function ($view) {
        return view("mail.$view", [
            'name' => 'Test User',
            'code' => '123456',
        ]);
    });
}

<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DocsTokenCheck;

Route::prefix('docs')->group(function () {
    Scramble::registerUiRoute('api');
    Scramble::registerJsonSpecificationRoute('api.json');
})->middleware([DocsTokenCheck::class]);

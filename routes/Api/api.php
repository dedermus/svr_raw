<?php

use Svr\Raw\Middleware\ApiValidationErrors;
use Illuminate\Support\Facades\Route;
use Svr\Raw\Controllers\Api\ApiFromSelexBeefController;

/*
|--------------------------------------------------------------------------
| Laravel Roles API RAW Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('api')
    ->prefix('api')
    ->middleware(ApiValidationErrors::class)
    ->group(function () {
        Route::get('from-selex-beef/',      [ApiFromSelexBeefController::class, 'index']);      // Для получения списка записей
        Route::post('from-selex-beef/',     [ApiFromSelexBeefController::class, 'store']);      // Для создания новой записи
        Route::put('from-selex-beef/{id}',  [ApiFromSelexBeefController::class, 'update']);     // Для обновления существующей записи
    });

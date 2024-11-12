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
    ->prefix('v1')
    ->middleware(ApiValidationErrors::class)
    ->group(function () {
        Route::post('selex/get_animals/',       [ApiFromSelexBeefController::class, 'get_animals']);      // Получение списка записей по GUID_SVR.

//        Route::post('selex/',       [ApiFromSelexBeefController::class, 'store']);      // Для создания новой записи
//        Route::post('selex/',       [ApiFromSelexBeefController::class, 'update']);     // Для обновления существующей записи
    });

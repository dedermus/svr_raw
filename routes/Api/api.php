<?php

use Illuminate\Support\Facades\Route;
use Svr\Raw\Controllers\Api\ApiSelexController;

/*
|--------------------------------------------------------------------------
| Laravel Roles API RAW Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix(config('svr.api_prefix'))->group(function () {

    /** Авторизация */
    Route::post('selex/login', [ApiSelexController::class, 'selexLogin']);

    /** Передача данных в СВР со стороны модуля обмена */
    Route::post('selex/send_animals', [ApiSelexController::class, 'selexSendAnimals'])->middleware([
       'auth:svr_api',
       'api'
    ]);

    /** Получение данных из СВР со стороны модуля обмена */
    Route::post('selex/check_animals', [ApiSelexController::class, 'selexCheckAnimals'])->middleware([
       'auth:svr_api',
       'api'
    ]);

});

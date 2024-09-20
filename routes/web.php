<?php

use Svr\Raw\Controllers\FromSelexSheepController;
use Svr\Raw\Controllers\FromSelexBeefController;
use Svr\Raw\Controllers\FromSelexMilkController;

Route::resource('raw_milk', FromSelexSheepController::class);
Route::resource('raw_beef', FromSelexBeefController::class);
Route::resource('raw_sheep', FromSelexMilkController::class);

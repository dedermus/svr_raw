<?php

use Svr\Raw\Controllers\FromSelexSheepController;
use Svr\Raw\Controllers\FromSelexBeefController;
use Svr\Raw\Controllers\FromSelexMilkController;

Route::resource('svr-from_selex_milk', FromSelexSheepController::class);
Route::resource('svr-from_selex_beef', FromSelexBeefController::class);
Route::resource('svr-from_selex_sheep', FromSelexMilkController::class);

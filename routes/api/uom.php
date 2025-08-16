<?php

use App\Http\Controllers\UomController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'uom', 'middleware' => 'auth'], function () {
    Route::get('/', [UomController::class, 'index']);
    Route::post('/', [UomController::class, 'store']);
});

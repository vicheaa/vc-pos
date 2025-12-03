<?php

use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth', 'prefix' => '/stocks'], function () {
    // Get all
    Route::get('/',                         [StockController::class, 'index']);

    // Create new
    Route::post('/',                        [StockController::class, 'store']);

    // Update
    Route::put('/{id}',                     [StockController::class, 'update']);

    // Delete
    Route::delete('/{id}',                  [StockController::class, 'destroy']);
});

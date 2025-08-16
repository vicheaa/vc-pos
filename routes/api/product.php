<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'product', 'middleware' => 'auth'], function () {
    Route::get('/',                     [ProductController::class, 'index']);
    Route::post('/',                    [ProductController::class, 'store']);
    Route::get('/{category_code}',      [ProductController::class, 'category_product_list']);
});

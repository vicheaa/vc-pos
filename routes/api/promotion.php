<?php

use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ProductPromotionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Promotion routes
    Route::get('/promotion',                    [PromotionController::class, 'index']);
    Route::post('/promotion',                   [PromotionController::class, 'store']);
    Route::get('/promotion/{promotion}',        [PromotionController::class, 'show']);
    Route::put('/promotion/{promotion}',        [PromotionController::class, 'update']);
    Route::delete('/promotion/{promotion}',     [PromotionController::class, 'destroy']);
    Route::get('/promotion/active/list',        [PromotionController::class, 'active_promotions']);

    // Product Promotion routes
    Route::get('/product-promotion',                        [ProductPromotionController::class, 'index']);
    Route::post('/product-promotion',                       [ProductPromotionController::class, 'store']);
    Route::get('/product-promotion/{productPromotion}',     [ProductPromotionController::class, 'show']);
    Route::put('/product-promotion/{productPromotion}',     [ProductPromotionController::class, 'update']);
    Route::delete('/product-promotion/{productPromotion}',  [ProductPromotionController::class, 'destroy']);
    Route::get('/product-promotion/product/list',           [ProductPromotionController::class, 'product_promotions']);
});

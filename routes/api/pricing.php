<?php

use App\Http\Controllers\PricingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/pricing', [PricingController::class, 'index']);
    Route::post('/pricing', [PricingController::class, 'store']);
    Route::get('/pricing/{pricing}', [PricingController::class, 'show']);
    Route::put('/pricing/{pricing}', [PricingController::class, 'update']);
    Route::delete('/pricing/{pricing}', [PricingController::class, 'destroy']);
    Route::get('/pricing/product/list', [PricingController::class, 'product_pricing_list']);
});

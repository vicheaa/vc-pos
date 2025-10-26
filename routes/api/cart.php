<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Route to get all applicable promotions for a single product
Route::post('/cart/check-promotion', [CartController::class, 'checkProductPromotion']);
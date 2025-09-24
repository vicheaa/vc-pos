<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::group(['prefix' => 'customers', 'middleware' => 'auth'], function () {
    Route::get('/', [CustomerController::class, 'index']);
});

<?php

use App\Http\Controllers\SequenceNumberController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth', 'prefix' => 'sequence-numbers'], function () {
    Route::get('/',                     [SequenceNumberController::class, 'index']);
    Route::post('/',                    [SequenceNumberController::class, 'store']);
    Route::get('/{sequenceNumber}',     [SequenceNumberController::class, 'show']);
    Route::put('/{sequenceNumber}',     [SequenceNumberController::class, 'update']);
    Route::delete('/{sequenceNumber}',  [SequenceNumberController::class, 'destroy']);
    
    // Testing endpoint
    Route::post('/generate',            [SequenceNumberController::class, 'generate']);
});

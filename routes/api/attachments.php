<?php

use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('attachments')->group(function () {
    Route::get('/',             [AttachmentController::class, 'index']);
    Route::post('/upload',      [AttachmentController::class, 'upload']);
    Route::delete('/delete',    [AttachmentController::class, 'destroy']);
});

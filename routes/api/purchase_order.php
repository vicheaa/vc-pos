<?php

use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth', 'prefix' => '/purchase-order'], function () {
    // Get all purchase orders
    Route::get('/',                         [PurchaseOrderController::class, 'index']);

    // Get specific purchase order
    Route::get('/{purchaseOrder}',           [PurchaseOrderController::class, 'show']);

    // Create new purchase order
    Route::post('/',                        [PurchaseOrderController::class, 'store']);

    // Update purchase order
    Route::put('/{purchaseOrder}',          [PurchaseOrderController::class, 'update']);

    // Delete purchase order
    Route::delete('/{purchaseOrder}',       [PurchaseOrderController::class, 'destroy']);

    // Get purchase order items
    Route::get('/{purchaseOrder}/items',    [PurchaseOrderController::class, 'items']);

    // Create purchase order items
    Route::post('/{purchaseOrder}/items',   [PurchaseOrderController::class, 'storeItem']);

    // Update purchase order items
    Route::put('/{purchaseOrder}/items',    [PurchaseOrderController::class, 'updateItem']);

    // Delete purchase order items
    Route::delete('/{purchaseOrder}/items', [PurchaseOrderController::class, 'destroyItem']);
});
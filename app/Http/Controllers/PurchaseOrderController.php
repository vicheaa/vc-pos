<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Services\StockService;
use App\Services\SequenceNumberService;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::all();
        return ApiResponse::success($purchaseOrders, 'Purchase orders retrieved successfully', 200, 'purchase_orders');
    }

    protected $stockService;
    protected $sequenceNumberService;

    public function __construct(
        StockService $stockService,
        SequenceNumberService $sequenceNumberService
    ) {
        $this->stockService = $stockService;
        $this->sequenceNumberService = $sequenceNumberService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseOrderRequest $request)
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $validated = $request->validated();
                
                // 1. Calculate totals
                $totalAmount = 0;
                $totalDiscount = 0;
                
                foreach ($validated['items'] as $item) {
                     $lineTotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);
                     $totalAmount += $item['quantity'] * $item['price'];
                     $totalDiscount += ($item['discount'] ?? 0);
                }
                
                $grandTotal = $totalAmount - $totalDiscount;

                // 2. Create Purchase Order
                $po = PurchaseOrder::create([
                    'po_no'             => $this->sequenceNumberService->generateNextNumber('po'),
                    'supplier_name'     => $validated['supplier_name'] ?? '',
                    'supplier_phone'    => $validated['supplier_phone'] ?? '',
                    'po_date'           => $validated['po_date'] ?? now(),
                    'note'              => $validated['note'] ?? '',
                    'shop_id'           => $validated['shop_id'],
                    'status'            => $validated['status'] ?? 'pending',
                    'total_amount'      => $totalAmount,
                    'total_discount'    => $totalDiscount,
                    'grand_total'       => $grandTotal,
                    'created_by'        => $request->user() ? $request->user()->id : null,
                ]);

                // 3. Create Items and prepare stock data
                $stockItems = [];
                
                foreach ($validated['items'] as $item) {
                    $lineTotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);
                    
                    $po->items()->create([
                        'product_code' => $item['product_code'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'] ?? 0,
                        'total' => $lineTotal,
                    ]);

                    $stockItems[] = [
                        'product_code' => $item['product_code'],
                        'quantity' => $item['quantity']
                    ];
                }

                // 4. Update Stock (Only if status is approved/completed, or if we assume creating a PO immediately adds stock)
                // For this implementation, I will assume we add stock immediately if status is NOT pending, or user explicitly wants it. 
                // However, standard flow is PO -> Receive -> Stock. 
                // Given "integrate with stock service" instruction, I will add it now, potentially filtering by status if provided.
                // Let's assume 'approved' or default implies stock entry for this MVP request.
                
                if (($validated['status'] ?? 'pending') == 'approved') {
                     $this->stockService->createTransaction(
                        [
                            'shop_id' => $po->shop_id,
                            'type' => 'PO-RECEIVE',
                            'created_by' => $po->created_by,
                            'po_no' => $po->po_no,
                            'details' => 'Purchase Order ' . $po->po_no
                        ],
                        $stockItems
                    );
                }

                return ApiResponse::success($po->load('items'), 'Purchase Order created successfully', 201);
            });
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}

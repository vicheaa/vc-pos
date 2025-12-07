<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Http\Helpers\ApiResponse;
use App\Services\StockService;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = Stock::with('product');
        
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        $stock = $query->paginate(20);
        return ApiResponse::paginated($stock);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_code'  => 'required|exists:products,code',
            'quantity'      => 'required|numeric',
            'type'          => 'required|string',
            'shop_id'       => 'nullable|exists:shops,id'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        try {
            $stock = $this->stockService->adjustStock(
                $request->product_code,
                $request->quantity,
                $request->type,
                $request->shop_id,
                $request->user() ? $request->user()->id : null
            );
            return ApiResponse::success($stock);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        // For stock, we usually don't "update" a record directly in a way that sets absolute values 
        // without tracking. 
        // However, if we must support direct update (e.g. inventory count correction), we should treat it 
        // as an adjustment from the current value to the new value.
        
        $stock = Stock::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'type' => 'required|string', // Reason for update
        ]);

        if ($validator->fails()) {
             return ApiResponse::error($validator->errors()->first(), 422);
        }

        $diff = $request->quantity - $stock->quantity;
        
        if ($diff == 0) {
            return ApiResponse::success($stock);
        }

        try {
            $updatedStock = $this->stockService->adjustStock(
                $stock->product_code,
                $diff,
                $request->type, // e.g., 'CORRECTION'
                $stock->shop_id,
                $request->user() ? $request->user()->id : null
            );
            return ApiResponse::success($updatedStock);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        // Deleting stock usually means setting q to 0 or removing the tracking record?
        // Be careful here. For now keeping it simple but really we should probably soft delete or 0 it out.
        $stock = Stock::findOrFail($id);
        $stock->delete();
        return ApiResponse::success($stock);
    }
}

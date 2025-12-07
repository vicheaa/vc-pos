<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\ApiResponse;
use App\Services\StockService;
use App\Services\SequenceNumberService;
class OrderController extends Controller
{
    protected $stockService;
    protected $sequenceNumberService;

    public function __construct(
        StockService $stockService, 
        SequenceNumberService $sequenceNumberService
    ) {
        $this->stockService = $stockService;
        $this->sequenceNumberService = $sequenceNumberService;
    }

    // protected function generateInvoiceNumber()
    // {
    //     return $this->sequenceNumberService->generateNextNumber('invoice');
    // }
    
    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        $validated = $request->validate([
            'customer_id'           => 'nullable|exists:customers,id',
            'items'                 => 'required|array|min:1',
            'items.*.product_code'  => 'required|exists:products,code',
            'items.*.quantity'      => 'required|numeric|min:1',
            'items.*.promotion_id'  => 'nullable|exists:promotions,id',
        ]);

        // 2. Wrap the entire operation in a database transaction
        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalDiscount = 0;

            // 3. Loop through items to calculate totals and check stock
            foreach ($validated['items'] as $itemData) {
                $product = Product::find($itemData['product_code']);
                // $stock = Stock::where('product_code', $product->code)->first();

                // // Check if there is enough stock
                // if (!$stock || $stock->quantity < $itemData['quantity']) {
                //     throw new \Exception('Not enough stock for product: ' . $product->name);
                // }

                // Use a function to calculate price and discount (like your check-promotion endpoint)
                $priceDetails = $this->calculateItemPrice($product, $itemData['quantity'], $itemData['promotion_id'] ?? null);
                $subtotal += $priceDetails['subtotal'];
                $totalDiscount += $priceDetails['total_discount'];
            }

            $grandTotal = $subtotal - $totalDiscount;

            // 4. Create the main Order record
            $order = Order::create([
                'invoice_no'     => $this->sequenceNumberService->generateNextNumber('invoice'),
                'user_id'        => 1,
                'customer_id'    => $validated['customer_id'] ?? null,
                'subtotal'       => $subtotal,
                'total_discount' => $totalDiscount,
                'grand_total'    => $grandTotal,
                'status'         => 'COMPLETED',
            ]);

            // 5. Create Order Items and update stock
            // 5. Create Order Items and prepare stock data
            $stockItems = [];
            foreach ($validated['items'] as $itemData) {
                $product = Product::find($itemData['product_code']);
                $priceDetails = $this->calculateItemPrice($product, $itemData['quantity'], $itemData['promotion_id'] ?? null);

                $order->orderItems()->create([
                    'product_code'    => $product->code,
                    'quantity'        => $itemData['quantity'],
                    'unit_price'      => $priceDetails['unit_price'],
                    'promotion_id'    => $itemData['promotion_id'] ?? null,
                    'discount_amount' => $priceDetails['total_discount'],
                    'line_total'      => $priceDetails['final_amount'],
                ]);

                // Collect stock item data
                $stockItems[] = [
                    'product_code' => $product->code,
                    'quantity'     => $itemData['quantity']
                ];
            }

            // 6. Record stock movement (Batch)
            $user = $request->user();
            $this->stockService->createTransaction(
                [
                    'shop_id'    => $order->shop_id,
                    'type'       => 'SALE',
                    'created_by' => $user->id ?? null,
                    'invoice_no' => $order->invoice_no
                ],
                $stockItems
            );

            // 7. If everything is successful, commit the transaction
            DB::commit();

            return ApiResponse::success($order->load('orderItems'), 'Order created successfully.');
        } catch (\Exception $e) {
            // 8. If anything fails, roll back the transaction
            DB::rollBack();
            return ApiResponse::error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Helper function to calculate item pricing based on promotions.
     * This can be a more complex version of your 'checkProductPromotion' logic.
     */
    private function calculateItemPrice(Product $product, $quantity, $promotionId = null)
    {
        $subtotal = $product->selling_price * $quantity;
        $totalDiscount = 0;

        // If a specific promotion is provided, use it
        if ($promotionId) {
            $promotion = Promotion::find($promotionId);

            if (
                $promotion && $promotion->is_active &&
                $promotion->start_date <= now() &&
                ($promotion->end_date >= now() || $promotion->end_date === null)
            ) {

                switch ($promotion->type) {
                    case 'percentage':
                        $totalDiscount = $subtotal * ($promotion->value / 100);
                        break;
                    case 'fixed':
                        $totalDiscount = $promotion->value * $quantity;
                        break;
                }
            }
        }

        $finalAmount = $subtotal - $totalDiscount;

        return [
            'unit_price' => $product->selling_price,
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'final_amount' => $finalAmount,
        ];
    }

    public function index()
    {
        $orders = Order::paginate(20);
        return ApiResponse::paginated($orders, 200, 'orders');
    }

    public function show($id)
    {
        $order = Order::find($id)->load('orderItems');
        return ApiResponse::success($order, 'Order fetched successfully.');
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $order->update($request->all());
        return ApiResponse::success($order, 'Order updated successfully.');
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        $order->delete();
        return ApiResponse::success(null, 'Order deleted successfully.');
    }
}

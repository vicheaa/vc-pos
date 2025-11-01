<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    /**
     * Checks multiple products and quantities against all valid promotions
     * and returns the fin  al calculated prices for each product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkProductPromotion(Request $request)
    {
        // 1. Validate the incoming data
        $validated = $request->validate([
            '*.product_code'    => ['required', 'string', Rule::exists('products', 'code')],
            '*.qty'             => ['required', 'integer', 'min:1'],
        ]);

        $results = [];

        // 2. Process each product in the request
        foreach ($validated as $item) {
            $product = Product::findOrFail($item['product_code']);
            $quantity = $item['qty'];

            // 3. Get all currently valid promotions for this product
            $validPromotions = $product->promotions()
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where(function ($query) {
                    $query->where('end_date', '>=', now())
                        ->orWhereNull('end_date');
                })
                ->get();

            $bestDiscount = 0;
            $appliedPromotionName = null;
            $appliedPromotionId = null;

            // 4. Loop through promotions to find the best one
            foreach ($validPromotions as $promotion) {
                $currentDiscount = 0;

                switch ($promotion->type) {
                    case 'percentage':
                        $currentDiscount = ($product->selling_price * $quantity) * ($promotion->value / 100);
                        break;

                    case 'fixed':
                        $currentDiscount = $promotion->value * $quantity;
                        break;
                }

                // If this promotion is better, we'll use it
                if ($currentDiscount > $bestDiscount) {
                    $bestDiscount = $currentDiscount;
                    $appliedPromotionName = $promotion->name;
                    $appliedPromotionId = $promotion->id;
                }
            }

            // 5. Calculate final prices
            $subtotal = $product->selling_price * $quantity;
            $finalAmount = $subtotal - $bestDiscount;

            // 6. Add result for this product
            $results[] = [
                'product_code'      => $product->code,
                'qty'               => $quantity,
                'unit_price'        => (float) $product->selling_price,
                'subtotal'          => round($subtotal, 2),
                'total_discount'    => round($bestDiscount, 2),
                'final_amount'      => round($finalAmount, 2),
                'promotion_id'      => $appliedPromotionId,
                'promotion_applied' => $appliedPromotionName,
            ];
        }

        // 7. Return the structured response
        return response()->json($results);
    }
}

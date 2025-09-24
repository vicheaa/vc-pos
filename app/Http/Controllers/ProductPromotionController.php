<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Models\ProductPromotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductPromotionController extends Controller
{

    public function index()
    {
        $productPromotions = ProductPromotion::with(['product', 'promotion'])->paginate(20);
        return ApiResponse::paginated($productPromotions, 200, 'product_promotions');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => [
                'required',
                'string',
                'exists:products,code',
                // Prevent duplicate product_code + promotion_id pairs
                Rule::unique('product_promotions')->where(fn($q) => $q->where('promotion_id', $request->input('promotion_id'))),
            ],
            'promotion_id' => ['required', 'exists:promotions,id'],
        ]);

        $productPromotion = ProductPromotion::create([
            'product_code' => $validated['product_code'],
            'promotion_id' => $validated['promotion_id'],
        ]);
        return ApiResponse::success(data: $productPromotion, message: 'Product promotion created successfully');
    }

    public function show(ProductPromotion $productPromotion)
    {
        $productPromotion->load(['product', 'promotion']);
        return ApiResponse::success(data: $productPromotion);
    }

    public function update(Request $request, ProductPromotion $productPromotion)
    {
        $validated = $request->validate([
            'product_code' => [
                'sometimes',
                'string',
                'exists:products,code',
                Rule::unique('product_promotions')
                    ->ignore($productPromotion->id)
                    ->where(fn($q) => $q->where('promotion_id', $request->input('promotion_id', $productPromotion->promotion_id))),
            ],
            'promotion_id' => [
                'sometimes',
                'exists:promotions,id',
                Rule::unique('product_promotions')
                    ->ignore($productPromotion->id)
                    ->where(fn($q) => $q->where('product_code', $request->input('product_code', $productPromotion->product_code))),
            ],
        ]);

        $productPromotion->update(array_filter([
            'product_code' => $validated['product_code'] ?? null,
            'promotion_id' => $validated['promotion_id'] ?? null,
        ], fn($v) => !is_null($v)));
        return ApiResponse::success(data: $productPromotion, message: 'Product promotion updated successfully');
    }

    public function destroy(ProductPromotion $productPromotion)
    {
        $productPromotion->delete();
        return ApiResponse::success(message: 'Product promotion deleted successfully');
    }

    public function product_promotions(Request $request)
    {
        $request->validate([
            'product_code' => ['required', 'string', 'exists:products,code'],
        ]);
        $product_code = $request->product_code;
        $productPromotions = ProductPromotion::with(['product', 'promotion'])
            ->where('product_code', $product_code)
            ->paginate(20);
        return ApiResponse::paginated($productPromotions, 200, 'product_promotions');
    }
}

<?php

namespace App\Http\Controllers;

use App\Contracts\PromotionServiceInterface;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function __construct(
        protected PromotionServiceInterface $promotionService
    ) {}

    public function index()
    {
        $promotions = Promotion::paginate(20);
        return ApiResponse::paginated($promotions, 200, 'promotions');
    }

    public function store(StorePromotionRequest $request)
    {
        $promotion = $this->promotionService->createPromotion($request);
        return ApiResponse::success(data: $promotion, message: 'Promotion reated successfully');
    }

    public function show(Promotion $promotion)
    {
        $promotion->load('products');
        return response()->json($promotion);
    }

    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $promotion = $this->promotionService->updatePromotion($request, $promotion);
        return ApiResponse::success(data: $promotion, message: 'Promotion updated successfully');
    }

    public function destroy(Promotion $promotion)
    {
        $this->promotionService->deletePromotion($promotion);
        return ApiResponse::success(message: 'Promotion deleted successfully');
    }

    public function active_promotions()
    {
        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->paginate(20);
        return ApiResponse::paginated($promotions);
    }
}

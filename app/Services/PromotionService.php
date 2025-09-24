<?php

namespace App\Services;

use App\Contracts\PromotionServiceInterface;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionService implements PromotionServiceInterface
{
    public function createPromotion(Request $request): Promotion
    {
        $promotion = Promotion::create($request->all());
        return $promotion;
    }

    public function updatePromotion(Request $request, Promotion $promotion): Promotion
    {
        $promotion->update($request->all());
        return $promotion;
    }

    public function deletePromotion(Promotion $promotion): bool
    {
        return $promotion->delete();
    }
}

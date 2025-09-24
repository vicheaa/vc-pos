<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use App\Models\Promotion;

interface PromotionServiceInterface
{
    public function createPromotion(Request $request): Promotion;
    public function updatePromotion(Request $request, Promotion $promotion): Promotion;
    public function deletePromotion(Promotion $promotion): bool;
}

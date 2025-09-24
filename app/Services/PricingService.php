<?php

namespace App\Services;

use App\Contracts\PricingServiceInterface;
use App\Models\Pricing;
use Illuminate\Http\Request;

class PricingService implements PricingServiceInterface
{
    public function createPricing(Request $request): Pricing
    {
        $pricing = Pricing::create($request->all());
        return $pricing;
    }

    public function updatePricing(Request $request, Pricing $pricing): Pricing
    {
        $pricing->update($request->all());
        return $pricing;
    }

    public function deletePricing(Pricing $pricing): bool
    {
        return $pricing->delete();
    }
}

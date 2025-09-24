<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use App\Models\Pricing;

interface PricingServiceInterface
{
    public function createPricing(Request $request): Pricing;
    public function updatePricing(Request $request, Pricing $pricing): Pricing;
    public function deletePricing(Pricing $pricing): bool;
}

<?php

namespace App\Http\Controllers;

use App\Contracts\PricingServiceInterface;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\StorePricingRequest;
use App\Http\Requests\UpdatePricingRequest;
use App\Models\Pricing;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function __construct(
        protected PricingServiceInterface $pricingService
    ) {}

    public function index()
    {
        $pricings = Pricing::with('product')->paginate(20);
        return ApiResponse::paginated($pricings);
    }

    public function store(StorePricingRequest $request)
    {
        $pricing = $this->pricingService->createPricing($request);
        return ApiResponse::success(data: $pricing, message: 'Pricing created successfully');
    }

    public function show(Pricing $pricing)
    {
        $pricing->load('product');
        return ApiResponse::success(data: $pricing);
    }

    public function update(UpdatePricingRequest $request, Pricing $pricing)
    {
        $pricing = $this->pricingService->updatePricing($request, $pricing);
        return ApiResponse::success(data: $pricing, message: 'Pricing updated successfully');
    }

    public function destroy(Pricing $pricing)
    {
        $this->pricingService->deletePricing($pricing);
        return ApiResponse::success(message: 'Pricing deleted successfully');
    }

    public function product_pricing_list(Request $request)
    {
        $product_code = $request->product_code;
        $pricings = Pricing::with('product')
            ->where('product_code', $product_code)
            ->paginate(20);
        return ApiResponse::paginated($pricings);
    }
}

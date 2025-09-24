<?php

namespace App\Providers;

use App\Contracts\AttacementServiceInterface;
use App\Contracts\CategoryServiceInterface;
use App\Contracts\PricingServiceInterface;
use App\Contracts\ProductServiceInterface;
use App\Contracts\PromotionServiceInterface;
use App\Contracts\UomServiceInterface;
use App\Services\AttachmentService;
use App\Services\CategoryService;
use App\Services\PricingService;
use App\Services\ProductService;
use App\Services\PromotionService;
use App\Services\UomService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AttacementServiceInterface::class, AttachmentService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(PricingServiceInterface::class, PricingService::class);
        $this->app->bind(UomServiceInterface::class, UomService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(PromotionServiceInterface::class, PromotionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

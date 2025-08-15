<?php

namespace App\Providers;

use App\Contracts\AttacementServiceInterface;
use App\Services\AttachmentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AttacementServiceInterface::class, AttachmentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

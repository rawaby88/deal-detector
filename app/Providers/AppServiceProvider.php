<?php

namespace App\Providers;

use App\Services\Product\DiscountCalculationService;
use App\Services\Product\DiscountCalculationServiceInterface;
use App\Services\Product\DiscountConfigurationService;
use App\Services\Product\DiscountConfigurationServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DiscountConfigurationServiceInterface::class, function ($app) {
            return new DiscountConfigurationService();
        });

        $this->app->singleton(DiscountCalculationServiceInterface::class, function ($app) {
            return new DiscountCalculationService(
                $app->make(DiscountConfigurationServiceInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict();
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }
}

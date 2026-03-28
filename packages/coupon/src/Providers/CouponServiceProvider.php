<?php

namespace ECommerce\Coupon\Providers;

use ECommerce\Coupon\Services\CouponService;
use Illuminate\Support\ServiceProvider;

class CouponServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/coupon.php', 'coupon');

        $this->app->bind(CouponService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/coupon.php' => config_path('coupon.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'ecommerce-coupon');
    }
}

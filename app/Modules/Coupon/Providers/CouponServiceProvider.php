<?php

namespace App\Modules\Coupon\Providers;

use App\Modules\Coupon\Services\CouponService;
use Illuminate\Support\ServiceProvider;

class CouponServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CouponService::class);
    }
}

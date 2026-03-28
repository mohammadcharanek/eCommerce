<?php

use App\Providers\AppServiceProvider;
use App\Modules\Product\Providers\ProductServiceProvider;
use App\Modules\Inventory\Providers\InventoryServiceProvider;
use App\Modules\Order\Providers\OrderServiceProvider;
use App\Modules\Cart\Providers\CartServiceProvider;
use App\Modules\Payment\Providers\PaymentServiceProvider;
use App\Modules\User\Providers\UserServiceProvider;
use App\Modules\Coupon\Providers\CouponServiceProvider;

return [
    AppServiceProvider::class,
    ProductServiceProvider::class,
    InventoryServiceProvider::class,
    OrderServiceProvider::class,
    CartServiceProvider::class,
    PaymentServiceProvider::class,
    UserServiceProvider::class,
    CouponServiceProvider::class,
];

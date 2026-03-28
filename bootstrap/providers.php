<?php

use App\Providers\AppServiceProvider;
use ECommerce\Core\CoreServiceProvider;
use ECommerce\User\Providers\UserServiceProvider;
use ECommerce\Product\Providers\ProductServiceProvider;
use ECommerce\Inventory\Providers\InventoryServiceProvider;
use ECommerce\Coupon\Providers\CouponServiceProvider;
use ECommerce\Cart\Providers\CartServiceProvider;
use ECommerce\Order\Providers\OrderServiceProvider;
use ECommerce\Payment\Providers\PaymentServiceProvider;

return [
    AppServiceProvider::class,
    CoreServiceProvider::class,
    UserServiceProvider::class,
    ProductServiceProvider::class,
    InventoryServiceProvider::class,
    CouponServiceProvider::class,
    CartServiceProvider::class,
    OrderServiceProvider::class,
    PaymentServiceProvider::class,
];

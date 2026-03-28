<?php

namespace App\Modules\Cart\Providers;

use App\Modules\Cart\Services\CartService;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CartService::class);
    }
}

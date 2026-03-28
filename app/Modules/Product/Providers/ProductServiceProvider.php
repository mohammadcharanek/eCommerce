<?php

namespace App\Modules\Product\Providers;

use App\Modules\Product\Events\ProductCreated;
use App\Modules\Product\Listeners\SendProductNotification;
use App\Modules\Product\Repositories\Eloquent\ProductRepository;
use App\Modules\Product\Repositories\Interfaces\ProductRepositoryInterface;
use App\Modules\Product\Services\ProductService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductService::class);
    }

    public function boot(): void
    {
        Event::listen(ProductCreated::class, SendProductNotification::class);
    }
}

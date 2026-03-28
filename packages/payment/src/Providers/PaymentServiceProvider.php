<?php

namespace ECommerce\Payment\Providers;

use ECommerce\Payment\Contracts\PaymentGatewayInterface;
use ECommerce\Payment\Events\PaymentProcessed;
use ECommerce\Payment\Services\Gateways\StripeGateway;
use ECommerce\Payment\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/payment.php', 'payment');

        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            $gateway = config('payment.default_gateway', 'stripe');
            return match ($gateway) {
                'stripe' => new StripeGateway(),
                default  => new StripeGateway(),
            };
        });
        $this->app->bind(PaymentService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/payment.php' => config_path('payment.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'ecommerce-payment');
    }
}

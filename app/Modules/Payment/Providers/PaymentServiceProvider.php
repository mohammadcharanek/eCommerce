<?php

namespace App\Modules\Payment\Providers;

use App\Modules\Payment\Contracts\PaymentGatewayInterface;
use App\Modules\Payment\Events\PaymentProcessed;
use App\Modules\Payment\Services\Gateways\StripeGateway;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            $gateway = config('ecommerce.payment.default_gateway', 'stripe');
            return match ($gateway) {
                'stripe' => new StripeGateway(),
                default  => new StripeGateway(),
            };
        });
        $this->app->bind(PaymentService::class);
    }
}

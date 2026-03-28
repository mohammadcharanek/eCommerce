<?php

namespace ECommerce\Payment\Services\Gateways;

use ECommerce\Payment\Contracts\PaymentGatewayInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function charge(float $amount, string $currency, array $paymentData): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount'               => (int) ($amount * 100),
                'currency'             => strtolower($currency),
                'payment_method'       => $paymentData['payment_method_id'] ?? null,
                'confirm'              => true,
                'return_url'           => $paymentData['return_url'] ?? config('app.url'),
                'automatic_payment_methods' => [
                    'enabled'          => true,
                    'allow_redirects'  => 'never',
                ],
            ]);

            return [
                'success'        => $paymentIntent->status === 'succeeded',
                'transaction_id' => $paymentIntent->id,
                'status'         => $paymentIntent->status,
                'payload'        => $paymentIntent->toArray(),
            ];
        } catch (ApiErrorException $e) {
            return [
                'success'  => false,
                'error'    => $e->getMessage(),
                'payload'  => [],
            ];
        }
    }

    public function refund(string $transactionId, float $amount): array
    {
        try {
            $refund = Refund::create([
                'payment_intent' => $transactionId,
                'amount'         => (int) ($amount * 100),
            ]);

            return [
                'success'  => $refund->status === 'succeeded',
                'refund_id'=> $refund->id,
                'status'   => $refund->status,
                'payload'  => $refund->toArray(),
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'payload' => [],
            ];
        }
    }

    public function getStatus(string $transactionId): string
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($transactionId);
            return $paymentIntent->status;
        } catch (ApiErrorException $e) {
            return 'error';
        }
    }
}

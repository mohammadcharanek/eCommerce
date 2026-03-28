<?php

namespace ECommerce\Payment\Contracts;

interface PaymentGatewayInterface
{
    public function charge(float $amount, string $currency, array $paymentData): array;
    public function refund(string $transactionId, float $amount): array;
    public function getStatus(string $transactionId): string;
}

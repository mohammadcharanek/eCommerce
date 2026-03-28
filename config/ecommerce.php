<?php

return [
    'modules' => [
        'product'   => true,
        'inventory' => true,
        'order'     => true,
        'cart'      => true,
        'payment'   => true,
        'coupon'    => true,
        'user'      => true,
    ],

    'currency' => [
        'default'   => env('DEFAULT_CURRENCY', 'USD'),
        'supported' => ['USD', 'EUR', 'GBP'],
    ],

    'payment' => [
        'default_gateway' => env('PAYMENT_GATEWAY', 'stripe'),
        'gateways'        => ['stripe'],
    ],

    'cache' => [
        'enabled' => env('ECOMMERCE_CACHE_ENABLED', true),
        'ttl'     => env('ECOMMERCE_CACHE_TTL', 3600),
        'prefix'  => 'ecommerce',
    ],

    'multi_tenant' => [
        'enabled'        => env('MULTI_TENANT_ENABLED', true),
        'identification' => env('TENANT_IDENTIFICATION', 'subdomain'), // subdomain, header, path
    ],

    'order' => [
        'number_prefix' => 'ORD',
        'tax_rate'      => env('TAX_RATE', 0.08),
    ],

    'inventory' => [
        'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 5),
    ],
];

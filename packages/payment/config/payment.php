<?php

return [
    // Payment module configuration
    'default_gateway' => env('PAYMENT_GATEWAY', 'stripe'),

    'stripe' => [
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
];

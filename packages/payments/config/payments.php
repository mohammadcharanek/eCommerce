<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Payment Methods
    |--------------------------------------------------------------------------
    | List of payment method identifiers available to customers.
    */
    'methods' => [
        'credit_card'      => 'Credit Card',
        'debit_card'       => 'Debit Card',
        'paypal'           => 'PayPal',
        'bank_transfer'    => 'Bank Transfer',
        'cash_on_delivery' => 'Cash on Delivery',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    */
    'currency' => env('PAYMENTS_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Payments Per Page
    |--------------------------------------------------------------------------
    */
    'per_page' => env('PAYMENTS_PER_PAGE', 15),

    /*
    |--------------------------------------------------------------------------
    | Gateway Configuration
    |--------------------------------------------------------------------------
    | Add your payment gateway credentials here. These are read by the
    | PaymentService when delegating to a gateway driver.
    */
    'gateways' => [
        'stripe' => [
            'key'    => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret'    => env('PAYPAL_SECRET'),
            'mode'      => env('PAYPAL_MODE', 'sandbox'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    */
    'route_prefix' => env('PAYMENTS_ROUTE_PREFIX', 'payments'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    */
    'middleware'     => ['web'],
    'api_middleware' => ['api'],
];

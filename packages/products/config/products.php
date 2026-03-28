<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Products Per Page
    |--------------------------------------------------------------------------
    | Default number of products shown per page when paginating.
    */
    'per_page' => env('PRODUCTS_PER_PAGE', 15),

    /*
    |--------------------------------------------------------------------------
    | Featured Products Limit
    |--------------------------------------------------------------------------
    | Number of featured products to show on the storefront.
    */
    'featured_limit' => env('PRODUCTS_FEATURED_LIMIT', 8),

    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    | Default low-stock warning threshold used when a product does not
    | define its own threshold.
    */
    'low_stock_threshold' => env('PRODUCTS_LOW_STOCK_THRESHOLD', 5),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    | URI prefix applied to all web routes registered by this package.
    */
    'route_prefix' => env('PRODUCTS_ROUTE_PREFIX', 'products'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    | Middleware applied to the package's web and API routes.
    */
    'middleware'     => ['web'],
    'api_middleware' => ['api'],
];

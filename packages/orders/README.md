# eCommerce Orders Package

An installable Laravel package that provides the **Orders** module for the eCommerce platform.

## Features

- **Order management**: Create, view, and manage orders with full address support
- **Order items**: Line items with product snapshot (name/SKU) for historical accuracy
- **Status workflow**: `pending → processing → shipped → delivered` with cancellation and refund support
- **Stock integration**: Automatically decrements stock on order creation, restores on cancellation
- **Tax & shipping**: Configurable tax rate and shipping amounts
- **Blade views**: Tailwind CSS UI (list, detail, create, status update)
- **API routes**: Full REST API under `/api/v1/orders`
- **Publishable**: config, migrations, seeders, views

## Requirements

- PHP ^8.2
- Laravel ^11.0
- `ecommerce/products` package

## Installation

```bash
composer require ecommerce/orders
```

For local development with path repository:

```json
{
    "repositories": [
        { "type": "path", "url": "./packages/products", "options": { "symlink": true } },
        { "type": "path", "url": "./packages/orders",   "options": { "symlink": true } }
    ]
}
```

## Publishing Assets

```bash
php artisan vendor:publish --tag=orders-config
php artisan vendor:publish --tag=orders-migrations
php artisan vendor:publish --tag=orders-views
php artisan vendor:publish --tag=orders-seeders
```

## Running Migrations & Seeder

```bash
php artisan migrate
php artisan db:seed --class="Ecommerce\Orders\Database\Seeders\OrdersSeeder"
```

## Configuration

| Key | Default | Description |
|-----|---------|-------------|
| `currency` | `USD` | Default order currency |
| `tax_rate` | `0.10` | Tax rate applied to subtotal (decimal) |
| `default_shipping` | `0` | Default shipping charge |
| `per_page` | `15` | Orders per page |
| `route_prefix` | `orders` | URI prefix for web routes |

## Web Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/orders` | `orders.index` | List orders |
| GET | `/orders/create` | `orders.create` | Create order form |
| POST | `/orders` | `orders.store` | Place order |
| GET | `/orders/{order}` | `orders.show` | View order |
| PATCH | `/orders/{order}/status` | `orders.updateStatus` | Update status |
| PATCH | `/orders/{order}/cancel` | `orders.cancel` | Cancel order |

## API Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api/v1/orders` | List orders (filterable) |
| POST | `/api/v1/orders` | Create order |
| GET | `/api/v1/orders/{order}` | Get order |
| PATCH | `/api/v1/orders/{order}/status` | Update status |
| PATCH | `/api/v1/orders/{order}/cancel` | Cancel order |

## Usage

```php
use Ecommerce\Orders\Services\OrderService;

class CheckoutController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function checkout(Request $request)
    {
        $order = $this->orderService->create(
            $request->only(['billing_name', 'billing_email', /* ... */]),
            [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 5, 'quantity' => 1],
            ]
        );

        return redirect()->route('payments.create', $order);
    }
}
```

## Testing

```bash
cd packages/orders
composer install
vendor/bin/phpunit
```

## License

MIT

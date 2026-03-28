# eCommerce Payments Package

An installable Laravel package that provides the **Payments** module for the eCommerce platform.

## Features

- **Payment processing**: Record and manage payments against orders
- **Multiple methods**: Credit card, debit card, PayPal, bank transfer, cash on delivery
- **Gateway-agnostic**: The `PaymentService` accepts gateway responses as arbitrary arrays — wire up Stripe, PayPal, or any other gateway in your application
- **Refunds**: Full or partial refund processing
- **Status tracking**: `pending`, `paid`, `failed`, `refunded`, `cancelled`
- **Blade views**: Tailwind CSS UI (list, pay form, payment detail with refund)
- **API routes**: REST API under `/api/v1/payments`
- **Publishable**: config, migrations, seeders, views

## Requirements

- PHP ^8.2
- Laravel ^11.0
- `ecommerce/products` and `ecommerce/orders` packages

## Installation

```bash
composer require ecommerce/payments
```

For local development with path repository:

```json
{
    "repositories": [
        { "type": "path", "url": "./packages/products", "options": { "symlink": true } },
        { "type": "path", "url": "./packages/orders",   "options": { "symlink": true } },
        { "type": "path", "url": "./packages/payments", "options": { "symlink": true } }
    ]
}
```

## Publishing Assets

```bash
php artisan vendor:publish --tag=payments-config
php artisan vendor:publish --tag=payments-migrations
php artisan vendor:publish --tag=payments-views
php artisan vendor:publish --tag=payments-seeders
```

## Running Migrations & Seeder

```bash
php artisan migrate
php artisan db:seed --class="Ecommerce\Payments\Database\Seeders\PaymentsSeeder"
```

## Configuration

| Key | Default | Description |
|-----|---------|-------------|
| `methods` | `[credit_card, debit_card, ...]` | Enabled payment methods |
| `currency` | `USD` | Default currency |
| `per_page` | `15` | Payments per page |
| `gateways.stripe.*` | env | Stripe API credentials |
| `gateways.paypal.*` | env | PayPal API credentials |
| `route_prefix` | `payments` | URI prefix for web routes |

## Web Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/payments` | `payments.index` | List payments |
| GET | `/payments/orders/{order}/pay` | `payments.create` | Payment form |
| POST | `/payments/orders/{order}/pay` | `payments.store` | Process payment |
| GET | `/payments/{payment}` | `payments.show` | View payment |
| POST | `/payments/{payment}/refund` | `payments.refund` | Refund payment |

## API Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api/v1/payments` | List payments |
| POST | `/api/v1/orders/{order}/payments` | Process payment |
| GET | `/api/v1/payments/{payment}` | Get payment |
| POST | `/api/v1/payments/{payment}/refund` | Refund payment |

## Usage

```php
use Ecommerce\Payments\Services\PaymentService;

class StripeWebhookController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function handle(Request $request)
    {
        $order = Order::findOrFail($request->input('metadata.order_id'));

        if ($request->input('type') === 'payment_intent.succeeded') {
            $this->paymentService->process($order, [
                'payment_method' => 'credit_card',
                'transaction_id' => $request->input('data.object.id'),
            ], $request->all());
        }
    }
}
```

## Environment Variables

```dotenv
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
PAYPAL_CLIENT_ID=...
PAYPAL_SECRET=...
PAYPAL_MODE=sandbox
```

## Testing

```bash
cd packages/payments
composer install
vendor/bin/phpunit
```

## License

MIT

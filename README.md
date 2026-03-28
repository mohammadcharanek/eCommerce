# eCommerce — Laravel Multi-Package Monorepo

A modular eCommerce platform built with **Laravel 11** and **Tailwind CSS**, structured as three independently installable Composer packages.

## Architecture

```
packages/
├── products/   → ecommerce/products   (Categories, Products)
├── orders/     → ecommerce/orders     (Orders, Order Items)
└── payments/   → ecommerce/payments   (Payments, Refunds)
```

Each package follows the standard Laravel package layout:

```
packages/<module>/
├── composer.json              # Package metadata & autoload
├── README.md                  # Package documentation
├── phpunit.xml                # Per-package test suite
├── config/                    # Publishable configuration
├── database/
│   ├── factories/             # Model factories
│   ├── migrations/            # Auto-loaded migrations
│   └── seeders/               # Publishable seeders
├── resources/views/           # Publishable Blade templates
├── routes/
│   ├── web.php                # Web routes
│   └── api.php                # API routes (v1)
├── src/
│   ├── <Module>ServiceProvider.php
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   └── Services/
└── tests/
    ├── TestCase.php           # Orchestra Testbench base
    └── Unit/
```

## Package dependency graph

```
ecommerce/payments
    └── ecommerce/orders
            └── ecommerce/products
```

## Quick start

### 1. Install dependencies

```bash
composer install
```

### 2. Set up environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure your database in `.env`, then migrate and seed

```bash
php artisan migrate

# Optional sample data
php artisan db:seed --class="Ecommerce\Products\Database\Seeders\ProductsSeeder"
php artisan db:seed --class="Ecommerce\Orders\Database\Seeders\OrdersSeeder"
php artisan db:seed --class="Ecommerce\Payments\Database\Seeders\PaymentsSeeder"
```

### 4. Start the development server

```bash
php artisan serve
```

## Publish package assets

```bash
# Configs
php artisan vendor:publish --tag=products-config
php artisan vendor:publish --tag=orders-config
php artisan vendor:publish --tag=payments-config

# Views (to customise the UI)
php artisan vendor:publish --tag=products-views
php artisan vendor:publish --tag=orders-views
php artisan vendor:publish --tag=payments-views
```

## Running tests

Each package has its own isolated test suite using [Orchestra Testbench](https://github.com/orchestral/testbench):

```bash
# Products
cd packages/products && composer install && vendor/bin/phpunit

# Orders
cd packages/orders && composer install && vendor/bin/phpunit

# Payments
cd packages/payments && composer install && vendor/bin/phpunit
```

## Using packages in another Laravel application

Add path repositories to the host application's `composer.json` and require the packages:

```json
{
    "repositories": [
        { "type": "path", "url": "./packages/products", "options": { "symlink": true } },
        { "type": "path", "url": "./packages/orders",   "options": { "symlink": true } },
        { "type": "path", "url": "./packages/payments", "options": { "symlink": true } }
    ],
    "require": {
        "ecommerce/products": "*",
        "ecommerce/orders":   "*",
        "ecommerce/payments": "*"
    }
}
```

Service providers are **auto-discovered** via the `extra.laravel.providers` key in each `composer.json`.

## Key URLs

| Package | Web | API |
|---------|-----|-----|
| Products | `/products`, `/categories` | `/api/v1/products`, `/api/v1/categories` |
| Orders | `/orders` | `/api/v1/orders` |
| Payments | `/payments` | `/api/v1/payments` |

## License

MIT — see [LICENSE](LICENSE).

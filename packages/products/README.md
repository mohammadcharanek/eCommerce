# eCommerce Products Package

An installable Laravel package that provides the **Products** module for the eCommerce platform.

## Features

- **Product management**: CRUD for products with stock, pricing, images, and SEO meta fields
- **Category management**: Hierarchical categories with parent/child relationships
- **Stock control**: Increment/decrement with low-stock warnings
- **Discount pricing**: Compare price with automatic discount percentage calculation
- **Scopes**: `active()`, `featured()`, `inStock()`
- **Blade views**: Tailwind CSS UI (index, show, create, edit, categories)
- **API routes**: Full REST API under `/api/v1/products` and `/api/v1/categories`
- **Publishable**: config, migrations, seeders, views

## Installation

### Via Composer (package repository)

```bash
composer require ecommerce/products
```

### Local development (path repository)

Add the path repository to your application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/products",
            "options": { "symlink": true }
        }
    ]
}
```

Then require the package:

```bash
composer require ecommerce/products:*
```

The `ProductsServiceProvider` is auto-discovered by Laravel.

## Publishing Assets

```bash
# Publish config
php artisan vendor:publish --tag=products-config

# Publish migrations
php artisan vendor:publish --tag=products-migrations

# Publish views (to customise)
php artisan vendor:publish --tag=products-views

# Publish seeders
php artisan vendor:publish --tag=products-seeders
```

## Running Migrations & Seeder

```bash
php artisan migrate
php artisan db:seed --class="Ecommerce\Products\Database\Seeders\ProductsSeeder"
```

## Configuration

After publishing the config file (`config/products.php`):

| Key | Default | Description |
|-----|---------|-------------|
| `per_page` | `15` | Products per page |
| `featured_limit` | `8` | Featured products shown on storefront |
| `low_stock_threshold` | `5` | Default low-stock warning level |
| `route_prefix` | `products` | URI prefix for web routes |
| `middleware` | `['web']` | Middleware for web routes |
| `api_middleware` | `['api']` | Middleware for API routes |

## Web Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/products` | `products.index` | List products |
| GET | `/products/create` | `products.create` | Create form |
| POST | `/products` | `products.store` | Store product |
| GET | `/products/{product}` | `products.show` | View product |
| GET | `/products/{product}/edit` | `products.edit` | Edit form |
| PUT | `/products/{product}` | `products.update` | Update product |
| DELETE | `/products/{product}` | `products.destroy` | Delete product |
| GET | `/categories` | `categories.index` | List categories |
| GET | `/categories/{category}` | `categories.show` | Category products |

## API Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api/v1/products` | List products (filterable, paginated) |
| POST | `/api/v1/products` | Create product |
| GET | `/api/v1/products/{product}` | Get product |
| PUT | `/api/v1/products/{product}` | Update product |
| DELETE | `/api/v1/products/{product}` | Delete product |
| GET | `/api/v1/categories` | List categories |
| GET | `/api/v1/categories/{category}` | Get category with products |

## Usage

```php
use Ecommerce\Products\Models\Product;
use Ecommerce\Products\Services\ProductService;

// Via model
$products = Product::active()->inStock()->with('category')->paginate(15);

// Via service (dependency injection)
class YourController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(Request $request)
    {
        $products = $this->productService->list($request->only(['search', 'category_id']));
        return view('your-view', compact('products'));
    }
}
```

## Testing

```bash
cd packages/products
composer install
vendor/bin/phpunit
```

## License

MIT

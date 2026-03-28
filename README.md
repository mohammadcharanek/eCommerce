# Enterprise eCommerce Platform

A modular, enterprise-grade e-commerce platform built with Laravel 13, featuring multi-tenancy, role-based access control, and a comprehensive REST API.

## 🚀 Features

- **Modular Architecture** — Each module (Product, Inventory, Order, Cart, Payment, User, Coupon) is self-contained
- **Multi-tenancy** — Subdomain and header-based tenant identification
- **Role-Based Access Control** — Admin, Vendor, Customer roles with granular permissions (Spatie)
- **REST API** — Fully documented with OpenAPI/Swagger annotations
- **Payment Processing** — Stripe integration with webhook support
- **Event-Driven** — Events, listeners, and jobs for async processing
- **Queue Support** — Redis-backed queue workers for emails, payments, and order processing
- **Docker Ready** — Complete Docker Compose setup with app, nginx, MySQL, and Redis

## 🏗️ Architecture

```
app/
├── Core/                    # Shared interfaces, base classes, traits
│   ├── Contracts/           # Repository & Service interfaces
│   ├── Repositories/        # BaseRepository (Eloquent)
│   ├── Services/            # BaseService
│   └── Traits/              # ApiResponse trait
└── Modules/                 # Feature modules
    ├── Product/             # Products, Categories, Attributes, Variants
    ├── Inventory/           # Stock management
    ├── Order/               # Orders, Order items, Status management
    ├── Cart/                # Shopping cart with coupon support
    ├── Payment/             # Stripe payment gateway
    ├── User/                # Auth, Users, Roles, Multi-tenancy
    └── Coupon/              # Discount coupon management
```

## 📋 Requirements

- PHP 8.3+
- MySQL 8.0+
- Redis 7+
- Composer 2+
- Node.js 18+ (for asset building)

## 🐳 Docker Setup (Recommended)

```bash
# Clone the repository
git clone <repo-url>
cd eCommerce

# Copy environment file
cp .env.example .env

# Configure your .env file (Stripe keys, etc.)

# Build and start containers
docker-compose up -d

# Run setup commands
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan l5-swagger:generate
```

The API will be available at `http://localhost:8080/api/v1`
Swagger docs at `http://localhost:8080/api/documentation`

## 🔧 Manual Installation

```bash
# Clone and install dependencies
git clone <repo-url>
cd eCommerce
composer install
npm install && npm run build

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Generate API documentation
php artisan l5-swagger:generate

# Start the development server
php artisan serve
```

## ⚙️ Environment Configuration

Key environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `STRIPE_KEY` | Stripe publishable key | — |
| `STRIPE_SECRET` | Stripe secret key | — |
| `CACHE_DRIVER` | Cache driver | `redis` |
| `QUEUE_CONNECTION` | Queue driver | `redis` |
| `MULTI_TENANT_ENABLED` | Enable multi-tenancy | `true` |
| `TENANT_IDENTIFICATION` | How to identify tenants | `subdomain` |
| `DEFAULT_CURRENCY` | Default currency | `USD` |
| `TAX_RATE` | Tax rate (decimal) | `0.08` |

## 📚 API Documentation

Interactive Swagger UI is available at `/api/documentation` after running:

```bash
php artisan l5-swagger:generate
```

### API Base URL: `/api/v1`

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register a new user |
| POST | `/auth/login` | Login and get token |
| POST | `/auth/logout` | Logout (revoke token) |
| GET | `/auth/me` | Get current user |

#### Products
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/products` | List products (supports `?search=`, `?category_id=`) |
| GET | `/products/{id}` | Get product details |
| POST | `/products` | Create product (admin/vendor) |
| PUT | `/products/{id}` | Update product (admin/vendor) |
| DELETE | `/products/{id}` | Delete product (admin) |

#### Orders
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/orders` | List user's orders |
| POST | `/orders` | Create order from cart |
| GET | `/orders/{id}` | Get order details |
| PUT | `/orders/{id}/status` | Update order status (admin) |
| PUT | `/orders/{id}/cancel` | Cancel order |

#### Cart
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/cart` | Get current cart |
| POST | `/cart/items` | Add item to cart |
| PUT | `/cart/items/{id}` | Update item quantity |
| DELETE | `/cart/items/{id}` | Remove item |
| POST | `/cart/coupon` | Apply coupon |

## 👥 Roles & Permissions

| Role | Permissions |
|------|-------------|
| `admin` | All permissions |
| `vendor` | View/create/edit products, view/manage orders, manage inventory |
| `customer` | View products/categories, create/view orders |

Default seeded credentials:
- Admin: `admin@example.com` / `password`
- Vendor: `vendor@example.com` / `password`
- Customer: `customer@example.com` / `password`

## 🔄 Queue Workers

Start queue workers for background job processing:

```bash
# Standard worker
php artisan queue:work

# With supervisor (recommended for production)
php artisan queue:work --sleep=3 --tries=3 --timeout=90

# Docker (automatically started)
docker-compose up queue -d
```

**Queued Jobs:**
- `SendOrderConfirmationEmail` — Sends order confirmation
- `ProcessOrder` — Background order processing
- `ProcessPayment` — Async payment processing

## 🧩 Module Reuse

Each module in `app/Modules/` is designed to be portable. To reuse a module in another Laravel project:

1. **Copy the module directory** to `app/Modules/{ModuleName}/` in your target project
2. **Copy the Core layer** (`app/Core/`) if not already present
3. **Copy migrations** from `database/migrations/` related to the module
4. **Register the service provider** in `bootstrap/providers.php`:
   ```php
   App\Modules\Product\Providers\ProductServiceProvider::class,
   ```
5. **Add routes** from the module's controller to your `routes/api.php`
6. **Install required packages** if not present (Sanctum, Spatie Permission)

## 🧪 Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run with coverage
php artisan test --coverage
```

## 🏢 Multi-Tenancy

Identify tenants via:

1. **Header**: `X-Tenant-ID: 1`
2. **Subdomain**: `tenant-name.yourdomain.com`

Tenant data is automatically scoped using the `HasTenant` trait and `TenantMiddleware`.

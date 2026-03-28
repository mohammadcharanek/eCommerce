<?php

/*
 * The API routes are now registered by each package's ServiceProvider.
 *
 * Each package loads its own routes from:
 *   packages/{module}/routes/api.php
 *
 * Packages and their route prefixes:
 *   - ecommerce/user     → /api/v1/auth, /api/v1/users, /api/v1/roles
 *   - ecommerce/product  → /api/v1/products, /api/v1/categories
 *   - ecommerce/inventory→ /api/v1/inventory
 *   - ecommerce/coupon   → /api/v1/coupons
 *   - ecommerce/cart     → /api/v1/cart
 *   - ecommerce/order    → /api/v1/orders
 *   - ecommerce/payment  → /api/v1/payments
 */

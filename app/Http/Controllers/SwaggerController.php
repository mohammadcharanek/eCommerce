<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *     title="eCommerce Platform API",
 *     version="1.0.0",
 *     description="Enterprise-grade modular e-commerce platform API",
 *     @OA\Contact(email="support@ecommerce.com"),
 *     @OA\License(name="MIT")
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Server(url="/api/v1", description="API V1")
 *
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 * @OA\Tag(name="Products", description="Product management endpoints")
 * @OA\Tag(name="Categories", description="Category management endpoints")
 * @OA\Tag(name="Cart", description="Shopping cart endpoints")
 * @OA\Tag(name="Orders", description="Order management endpoints")
 * @OA\Tag(name="Payments", description="Payment processing endpoints")
 * @OA\Tag(name="Coupons", description="Coupon management endpoints")
 * @OA\Tag(name="Inventory", description="Inventory management endpoints")
 * @OA\Tag(name="Users", description="User management endpoints")
 */
class SwaggerController extends Controller {}

<?php

namespace App\Modules\Cart\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Cart\Requests\AddToCartRequest;
use App\Modules\Cart\Resources\CartResource;
use App\Modules\Cart\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CartService $cartService) {}

    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Get current cart",
     *     @OA\Response(response=200, description="Cart data")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart(
            tenantId: (int) $request->header('X-Tenant-ID', 1),
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
        );
        return $this->successResponse(new CartResource($cart->load(['items.product', 'coupon'])));
    }

    /**
     * @OA\Post(
     *     path="/cart/items",
     *     tags={"Cart"},
     *     summary="Add item to cart",
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"product_id","quantity"},
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="variant_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Item added")
     * )
     */
    public function addItem(AddToCartRequest $request): JsonResponse
    {
        $cart = $this->cartService->getCart(
            tenantId: (int) $request->header('X-Tenant-ID', 1),
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
        );
        $this->cartService->addItem($cart, $request->product_id, $request->quantity, $request->variant_id);
        return $this->successResponse(new CartResource($cart->fresh(['items.product', 'coupon'])), 'Item added to cart');
    }

    /**
     * @OA\Put(
     *     path="/cart/items/{id}",
     *     tags={"Cart"},
     *     summary="Update cart item quantity",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"quantity"}, @OA\Property(property="quantity", type="integer"))
     *     ),
     *     @OA\Response(response=200, description="Cart item updated")
     * )
     */
    public function updateItem(Request $request, int $id): JsonResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $cart = $this->cartService->getCart(
            tenantId: (int) $request->header('X-Tenant-ID', 1),
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
        );
        $this->cartService->updateQuantity($cart, $id, $request->quantity);
        return $this->successResponse(new CartResource($cart->fresh(['items.product', 'coupon'])), 'Cart item updated');
    }

    /**
     * @OA\Delete(
     *     path="/cart/items/{id}",
     *     tags={"Cart"},
     *     summary="Remove cart item",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Item removed")
     * )
     */
    public function removeItem(Request $request, int $id): JsonResponse
    {
        $cart = $this->cartService->getCart(
            tenantId: (int) $request->header('X-Tenant-ID', 1),
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
        );
        $this->cartService->removeItem($cart, $id);
        return $this->successResponse(new CartResource($cart->fresh(['items.product', 'coupon'])), 'Item removed from cart');
    }

    /**
     * @OA\Post(
     *     path="/cart/coupon",
     *     tags={"Cart"},
     *     summary="Apply coupon to cart",
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"code"}, @OA\Property(property="code", type="string"))
     *     ),
     *     @OA\Response(response=200, description="Coupon applied"),
     *     @OA\Response(response=400, description="Invalid coupon")
     * )
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);
        try {
            $cart = $this->cartService->getCart(
                tenantId: (int) $request->header('X-Tenant-ID', 1),
                userId: $request->user()?->id,
                sessionId: $request->session()->getId(),
            );
            $cart = $this->cartService->applyCoupon($cart, $request->code);
            return $this->successResponse(new CartResource($cart), 'Coupon applied successfully');
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}

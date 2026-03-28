<?php

namespace ECommerce\Cart\Services;

use ECommerce\Cart\Models\Cart;
use ECommerce\Cart\Models\CartItem;
use ECommerce\Coupon\Models\Coupon;
use ECommerce\Coupon\Services\CouponService;
use ECommerce\Inventory\Services\InventoryService;
use ECommerce\Product\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly CouponService $couponService,
    ) {}

    public function getCart(int $tenantId, ?int $userId = null, ?string $sessionId = null): Cart
    {
        $query = Cart::active()->where('tenant_id', $tenantId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->with(['items.product', 'items.variant', 'coupon'])->firstOrCreate(
            ['tenant_id' => $tenantId, 'user_id' => $userId, 'session_id' => $sessionId],
            ['expires_at' => now()->addDays(7)]
        );
    }

    public function addItem(Cart $cart, int $productId, int $quantity, ?int $variantId = null): CartItem
    {
        $product = Product::findOrFail($productId);

        if (!$this->inventoryService->checkAvailability($productId, $quantity, $variantId)) {
            throw new \RuntimeException('Insufficient stock for this product.');
        }

        $price = $variantId
            ? $product->variants()->findOrFail($variantId)->price
            : $product->price;

        $existingItem = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity'   => $quantity,
            'price'      => $price,
        ]);
    }

    public function removeItem(Cart $cart, int $itemId): bool
    {
        return (bool) $cart->items()->where('id', $itemId)->delete();
    }

    public function updateQuantity(Cart $cart, int $itemId, int $quantity): CartItem
    {
        $item = $cart->items()->findOrFail($itemId);

        if (!$this->inventoryService->checkAvailability($item->product_id, $quantity, $item->variant_id)) {
            throw new \RuntimeException('Insufficient stock.');
        }

        $item->update(['quantity' => $quantity]);
        return $item->fresh();
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);
    }

    public function applyCoupon(Cart $cart, string $code): Cart
    {
        $coupon = $this->couponService->validate($code, $this->calculateSubtotal($cart));
        $cart->update(['coupon_id' => $coupon->id]);
        return $cart->fresh(['items', 'coupon']);
    }

    public function calculateTotals(Cart $cart): array
    {
        $subtotal = $this->calculateSubtotal($cart);
        $discount = 0.0;

        if ($cart->coupon && $cart->coupon->isValid()) {
            $discount = $cart->coupon->calculateDiscount($subtotal);
        }

        $taxRate = 0.08;
        $taxableAmount = $subtotal - $discount;
        $tax = round($taxableAmount * $taxRate, 2);
        $total = $taxableAmount + $tax;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax'      => $tax,
            'total'    => $total,
        ];
    }

    private function calculateSubtotal(Cart $cart): float
    {
        return (float) $cart->items->sum(fn($item) => $item->price * $item->quantity);
    }
}

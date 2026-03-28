<?php

namespace App\Modules\Order\Services;

use App\Core\Services\BaseService;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Services\CartService;
use App\Modules\Coupon\Models\CouponUse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Order\Events\OrderPlaced;
use App\Modules\Order\Events\OrderStatusUpdated;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderStatus;
use App\Modules\Order\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService extends BaseService
{
    public function __construct(
        OrderRepositoryInterface $repository,
        private readonly CartService $cartService,
        private readonly InventoryService $inventoryService,
    ) {
        parent::__construct($repository);
    }

    public function createOrder(Cart $cart, int $userId, array $addressData, string $currency = 'USD'): Order
    {
        return DB::transaction(function () use ($cart, $userId, $addressData, $currency) {
            $cart->load(['items.product', 'items.variant', 'coupon']);
            $totals = $this->cartService->calculateTotals($cart);

            $order = Order::create([
                'tenant_id'        => $cart->tenant_id,
                'user_id'          => $userId,
                'order_number'     => $this->generateOrderNumber(),
                'status'           => Order::STATUS_PENDING,
                'subtotal'         => $totals['subtotal'],
                'discount'         => $totals['discount'],
                'tax'              => $totals['tax'],
                'shipping'         => 0,
                'total'            => $totals['total'],
                'currency'         => $currency,
                'shipping_address' => $addressData['shipping_address'] ?? null,
                'billing_address'  => $addressData['billing_address'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name'       => $item->product->name,
                    'sku'        => $item->variant ? $item->variant->sku : $item->product->sku,
                    'price'      => $item->price,
                    'quantity'   => $item->quantity,
                    'subtotal'   => $item->price * $item->quantity,
                ]);

                $this->inventoryService->decreaseStock($item->product_id, $item->quantity, $item->variant_id);
            }

            if ($cart->coupon) {
                CouponUse::create([
                    'coupon_id'       => $cart->coupon_id,
                    'user_id'         => $userId,
                    'order_id'        => $order->id,
                    'discount_amount' => $totals['discount'],
                ]);
                $cart->coupon->increment('used_count');
            }

            $this->cartService->clearCart($cart);

            event(new OrderPlaced($order));

            return $order->load(['items', 'user']);
        });
    }

    public function updateStatus(int $orderId, string $status): Order
    {
        $order = Order::findOrFail($orderId);
        $currentStatus = OrderStatus::from($order->status);
        $newStatus = OrderStatus::from($status);

        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new \RuntimeException("Cannot transition order from {$order->status} to {$status}.");
        }

        $order->update(['status' => $status]);
        event(new OrderStatusUpdated($order, $order->status, $status));

        return $order->fresh(['items', 'payments', 'user']);
    }

    public function cancelOrder(int $orderId): Order
    {
        return $this->updateStatus($orderId, Order::STATUS_CANCELLED);
    }

    public function getOrder(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByUser($userId, $perPage);
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(Str::random(4)) . '-' . now()->format('YmdHis');
    }
}

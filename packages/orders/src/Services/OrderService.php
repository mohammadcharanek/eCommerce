<?php

namespace Ecommerce\Orders\Services;

use Ecommerce\Orders\Models\Order;
use Ecommerce\Orders\Models\OrderItem;
use Ecommerce\Products\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Retrieve a paginated list of orders.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with(['items']);

        if (! empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('billing_email', 'like', "%{$search}%")
                  ->orWhere('billing_name', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new order from a cart/items array.
     *
     * Each item in $items should be: ['product_id' => int, 'quantity' => int]
     */
    public function create(array $data, array $items): Order
    {
        return DB::transaction(function () use ($data, $items) {
            $subtotal = 0;

            $resolvedItems = [];
            foreach ($items as $item) {
                /** @var Product $product */
                $product = Product::findOrFail($item['product_id']);
                $product->decrementStock($item['quantity']);

                $lineTotal       = $product->price * $item['quantity'];
                $subtotal       += $lineTotal;
                $resolvedItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $product->price,
                    'subtotal'     => $lineTotal,
                ];
            }

            $taxAmount      = round($subtotal * config('orders.tax_rate', 0), 2);
            $shippingAmount = $data['shipping_amount'] ?? config('orders.default_shipping', 0);
            $discountAmount = $data['discount_amount'] ?? 0;
            $total          = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            $order = Order::create(array_merge($data, [
                'order_number'    => Order::generateOrderNumber(),
                'status'          => Order::STATUS_PENDING,
                'subtotal'        => $subtotal,
                'tax_amount'      => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'total'           => $total,
                'currency'        => $data['currency'] ?? config('orders.currency', 'USD'),
            ]));

            $order->items()->createMany($resolvedItems);

            return $order->load('items');
        });
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Order $order, string $status): Order
    {
        $allowed = [
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
            Order::STATUS_REFUNDED,
        ];

        if (! in_array($status, $allowed)) {
            throw new \InvalidArgumentException("Invalid order status [{$status}].");
        }

        $updates = ['status' => $status];

        if ($status === Order::STATUS_SHIPPED) {
            $updates['shipped_at'] = now();
        }

        if ($status === Order::STATUS_DELIVERED) {
            $updates['delivered_at'] = now();
        }

        $order->update($updates);

        return $order->fresh();
    }

    /**
     * Cancel an order and restore product stock.
     */
    public function cancel(Order $order): Order
    {
        if (! $order->isCancellable()) {
            throw new \RuntimeException("Order [{$order->order_number}] cannot be cancelled.");
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    $product?->incrementStock($item->quantity);
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);

            return $order->fresh();
        });
    }
}

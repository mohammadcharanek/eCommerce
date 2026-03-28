<?php

namespace App\Modules\Order\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'tenantId'        => $this->tenant_id,
            'userId'          => $this->user_id,
            'orderNumber'     => $this->order_number,
            'status'          => $this->status,
            'subtotal'        => (float) $this->subtotal,
            'discount'        => (float) $this->discount,
            'tax'             => (float) $this->tax,
            'shipping'        => (float) $this->shipping,
            'total'           => (float) $this->total,
            'currency'        => $this->currency,
            'notes'           => $this->notes,
            'shippingAddress' => $this->shipping_address,
            'billingAddress'  => $this->billing_address,
            'metadata'        => $this->metadata,
            'items'           => $this->whenLoaded('items', fn() => $this->items->map(fn($item) => [
                'id'        => $item->id,
                'productId' => $item->product_id,
                'variantId' => $item->variant_id,
                'name'      => $item->name,
                'sku'       => $item->sku,
                'price'     => (float) $item->price,
                'quantity'  => $item->quantity,
                'subtotal'  => (float) $item->subtotal,
            ])),
            'payments'        => $this->whenLoaded('payments', fn() => $this->payments->map(fn($p) => [
                'id'            => $p->id,
                'gateway'       => $p->gateway,
                'transactionId' => $p->transaction_id,
                'amount'        => (float) $p->amount,
                'currency'      => $p->currency,
                'status'        => $p->status,
            ])),
            'user'            => $this->whenLoaded('user', fn() => $this->user ? [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ] : null),
            'createdAt'       => $this->created_at?->toIso8601String(),
            'updatedAt'       => $this->updated_at?->toIso8601String(),
        ];
    }
}

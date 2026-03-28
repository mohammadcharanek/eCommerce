<?php

namespace App\Modules\Cart\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'tenantId'  => $this->tenant_id,
            'userId'    => $this->user_id,
            'sessionId' => $this->session_id,
            'expiresAt' => $this->expires_at?->toIso8601String(),
            'coupon'    => $this->whenLoaded('coupon', fn() => $this->coupon ? [
                'id'   => $this->coupon->id,
                'code' => $this->coupon->code,
                'type' => $this->coupon->type,
                'value'=> (float) $this->coupon->value,
            ] : null),
            'items'     => $this->whenLoaded('items', fn() => $this->items->map(fn($item) => [
                'id'        => $item->id,
                'productId' => $item->product_id,
                'variantId' => $item->variant_id,
                'quantity'  => $item->quantity,
                'price'     => (float) $item->price,
                'subtotal'  => (float) $item->subtotal,
                'product'   => $item->relationLoaded('product') ? [
                    'id'   => $item->product->id,
                    'name' => $item->product->name,
                    'sku'  => $item->product->sku,
                ] : null,
            ])),
        ];
    }
}

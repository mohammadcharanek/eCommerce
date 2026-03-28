<?php

namespace ECommerce\Product\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'categoryId'   => $this->category_id,
            'tenantId'     => $this->tenant_id,
            'name'         => $this->name,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'price'        => (float) $this->price,
            'comparePrice' => $this->compare_price ? (float) $this->compare_price : null,
            'sku'          => $this->sku,
            'isActive'     => (bool) $this->is_active,
            'isFeatured'   => (bool) $this->is_featured,
            'images'       => $this->images ?? [],
            'meta'         => $this->meta ?? [],
            'category'     => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'variants'     => $this->whenLoaded('variants', fn() => $this->variants->map(fn($v) => [
                'id'         => $v->id,
                'sku'        => $v->sku,
                'price'      => (float) $v->price,
                'attributes' => $v->attributes ?? [],
            ])),
            'inventory'    => $this->whenLoaded('inventory', fn() => $this->inventory ? [
                'quantity'          => $this->inventory->quantity,
                'reservedQuantity'  => $this->inventory->reserved_quantity,
                'availableQuantity' => $this->inventory->available_quantity,
                'isLowStock'        => $this->inventory->isLowStock(),
            ] : null),
            'createdAt'    => $this->created_at?->toIso8601String(),
            'updatedAt'    => $this->updated_at?->toIso8601String(),
        ];
    }
}

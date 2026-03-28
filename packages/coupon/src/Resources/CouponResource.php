<?php

namespace ECommerce\Coupon\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'tenantId'       => $this->tenant_id,
            'code'           => $this->code,
            'type'           => $this->type,
            'value'          => (float) $this->value,
            'minOrderAmount' => $this->min_order_amount ? (float) $this->min_order_amount : null,
            'maxUses'        => $this->max_uses,
            'usedCount'      => $this->used_count,
            'isActive'       => (bool) $this->is_active,
            'startsAt'       => $this->starts_at?->toIso8601String(),
            'expiresAt'      => $this->expires_at?->toIso8601String(),
            'createdAt'      => $this->created_at?->toIso8601String(),
        ];
    }
}

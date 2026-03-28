<?php

namespace ECommerce\Coupon\Models;

use ECommerce\User\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\CouponFactory
    {
        return \Database\Factories\CouponFactory::new();
    }
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED      = 'fixed';

    protected $fillable = [
        'tenant_id', 'code', 'type', 'value', 'min_order_amount',
        'max_uses', 'used_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'            => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_uses'         => 'integer',
            'used_count'       => 'integer',
            'is_active'        => 'boolean',
            'starts_at'        => 'datetime',
            'expires_at'       => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function couponUses(): HasMany
    {
        return $this->hasMany(CouponUse::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }
        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return round($orderAmount * ($this->value / 100), 2);
        }
        return min((float) $this->value, $orderAmount);
    }
}

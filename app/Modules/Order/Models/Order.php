<?php

namespace App\Modules\Order\Models;

use App\Modules\Payment\Models\Payment;
use App\Modules\User\Models\Tenant;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Database\Factories\OrderFactory
    {
        return \Database\Factories\OrderFactory::new();
    }

    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_CANCELLED  = 'cancelled';
    public const STATUS_REFUNDED   = 'refunded';

    protected $fillable = [
        'tenant_id', 'user_id', 'order_number', 'status',
        'subtotal', 'discount', 'tax', 'shipping', 'total',
        'currency', 'notes', 'shipping_address', 'billing_address', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'         => 'decimal:2',
            'discount'         => 'decimal:2',
            'tax'              => 'decimal:2',
            'shipping'         => 'decimal:2',
            'total'            => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address'  => 'array',
            'metadata'         => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }
}

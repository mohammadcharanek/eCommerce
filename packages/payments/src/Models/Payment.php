<?php

namespace Ecommerce\Payments\Models;

use Ecommerce\Orders\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Available payment statuses.
     */
    public const STATUS_PENDING   = 'pending';
    public const STATUS_PAID      = 'paid';
    public const STATUS_FAILED    = 'failed';
    public const STATUS_REFUNDED  = 'refunded';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Available payment methods.
     */
    public const METHOD_CREDIT_CARD  = 'credit_card';
    public const METHOD_DEBIT_CARD   = 'debit_card';
    public const METHOD_PAYPAL       = 'paypal';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_CASH_ON_DELIVERY = 'cash_on_delivery';

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'status',
        'amount',
        'currency',
        'gateway_response',
        'paid_at',
        'refunded_at',
        'refund_amount',
        'notes',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'refund_amount'    => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
        'refunded_at'      => 'datetime',
    ];

    /**
     * Get the order associated with this payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Determine if this payment was successful.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Determine if this payment can be refunded.
     */
    public function isRefundable(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Scope a query to only include paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope a query to filter by payment method.
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }
}

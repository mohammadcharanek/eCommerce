<?php

namespace ECommerce\Order\Models;

enum OrderStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Shipped    = 'shipped';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';
    case Refunded   = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::Processing => 'Processing',
            self::Shipped    => 'Shipped',
            self::Delivered  => 'Delivered',
            self::Cancelled  => 'Cancelled',
            self::Refunded   => 'Refunded',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return match($this) {
            self::Pending    => in_array($status, [self::Processing, self::Cancelled]),
            self::Processing => in_array($status, [self::Shipped, self::Cancelled]),
            self::Shipped    => in_array($status, [self::Delivered, self::Refunded]),
            self::Delivered  => in_array($status, [self::Refunded]),
            self::Cancelled  => false,
            self::Refunded   => false,
        };
    }
}

<?php

declare(strict_types=1);

namespace Orders\Domain\ValueObjects;

final class OrderStatusTransitions
{
    private const ALLOWED = [
        'pending_payment' => ['products_reserved', 'cancelled'],
        'products_reserved' => ['payment_processed', 'cancelled'],
        'payment_processed' => ['products_picked', 'cancelled'],
        'products_picked' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public static function canTransition(OrderStatus $from, OrderStatus $to): bool
    {
        $allowed = self::ALLOWED[$from->value] ?? [];
        return in_array($to->value, $allowed, true);
    }
}

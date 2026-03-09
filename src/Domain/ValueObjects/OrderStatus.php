<?php

declare(strict_types=1);

namespace Orders\Domain\ValueObjects;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PRODUCTS_RESERVED = 'products_reserved';
    case PAYMENT_PROCESSED = 'payment_processed';
    case PRODUCTS_PICKED = 'products_picked';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}

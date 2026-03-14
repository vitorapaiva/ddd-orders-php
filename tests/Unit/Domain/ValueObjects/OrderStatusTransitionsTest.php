<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Domain\ValueObjects;

use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Domain\ValueObjects\OrderStatusTransitions;
use PHPUnit\Framework\TestCase;

class OrderStatusTransitionsTest extends TestCase
{
    public function testCanTransitionFromPendingPaymentToProductsReserved(): void
    {
        $this->assertTrue(
            OrderStatusTransitions::canTransition(OrderStatus::PENDING_PAYMENT, OrderStatus::PRODUCTS_RESERVED)
        );
    }

    public function testCanTransitionFromPendingPaymentToCancelled(): void
    {
        $this->assertTrue(
            OrderStatusTransitions::canTransition(OrderStatus::PENDING_PAYMENT, OrderStatus::CANCELLED)
        );
    }

    public function testCannotTransitionFromPendingPaymentToPaymentProcessed(): void
    {
        $this->assertFalse(
            OrderStatusTransitions::canTransition(OrderStatus::PENDING_PAYMENT, OrderStatus::PAYMENT_PROCESSED)
        );
    }

    public function testCanTransitionFromProductsReservedToPaymentProcessed(): void
    {
        $this->assertTrue(
            OrderStatusTransitions::canTransition(OrderStatus::PRODUCTS_RESERVED, OrderStatus::PAYMENT_PROCESSED)
        );
    }

    public function testCannotTransitionFromDelivered(): void
    {
        $this->assertFalse(
            OrderStatusTransitions::canTransition(OrderStatus::DELIVERED, OrderStatus::SHIPPED)
        );
    }

    public function testCanTransitionFromShippedToDelivered(): void
    {
        $this->assertTrue(
            OrderStatusTransitions::canTransition(OrderStatus::SHIPPED, OrderStatus::DELIVERED)
        );
    }
}

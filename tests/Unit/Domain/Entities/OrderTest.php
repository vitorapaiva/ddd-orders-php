<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Domain\Entities;

use Orders\Domain\Entities\Order;
use Orders\Domain\ValueObjects\Item;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testCreatesOrderWithDefaults(): void
    {
        $order = TestHelpers::validOrder();

        $this->assertNotEmpty($order->toDto()->id);
        $this->assertSame('cust-1', $order->toDto()->customerId);
        $this->assertSame(OrderStatus::PENDING_PAYMENT, $order->getStatus());
        $this->assertSame(20.0, $order->toDto()->total);
    }

    public function testUpdateStatus(): void
    {
        $order = TestHelpers::validOrder();
        $order->updateStatus(OrderStatus::PRODUCTS_RESERVED);

        $this->assertSame(OrderStatus::PRODUCTS_RESERVED, $order->getStatus());
    }

    public function testUpdateStatusThrowsOnInvalidTransition(): void
    {
        $order = TestHelpers::validOrder();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invalid status transition');

        $order->updateStatus(OrderStatus::DELIVERED);
    }

    public function testThrowsWhenCustomerIdEmpty(): void
    {
        $addr = TestHelpers::validAddress();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer ID is required');

        new Order('', $addr, $addr, [new Item('p1', 1, 10.0)]);
    }

    public function testThrowsWhenItemsEmpty(): void
    {
        $addr = TestHelpers::validAddress();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('at least one item');

        new Order('cust-1', $addr, $addr, []);
    }

    public function testToDto(): void
    {
        $order = TestHelpers::validOrder();
        $dto = $order->toDto();

        $this->assertSame('cust-1', $dto->customerId);
        $this->assertSame('pending_payment', $dto->status);
        $this->assertCount(1, $dto->items);
    }
}

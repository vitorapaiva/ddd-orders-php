<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Domain\ValueObjects;

use Orders\Domain\ValueObjects\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testCreatesItem(): void
    {
        $item = new Item('p1', 2, 10.0);

        $this->assertSame('p1', $item->getProductId());
        $this->assertSame(2, $item->getQuantity());
        $this->assertSame(10.0, $item->getPrice());
    }

    public function testCalculateSubtotal(): void
    {
        $item = new Item('p1', 3, 5.5);
        $this->assertSame(16.5, $item->calculateSubtotal());
    }

    public function testThrowsWhenQuantityZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be greater than zero');

        new Item('p1', 0, 10.0);
    }

    public function testThrowsWhenPriceZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be greater than zero');

        new Item('p1', 1, 0.0);
    }

    public function testFromArray(): void
    {
        $item = Item::fromArray(['product_id' => 'p2', 'quantity' => 4, 'price' => 2.5]);

        $this->assertSame('p2', $item->getProductId());
        $this->assertSame(4, $item->getQuantity());
        $this->assertSame(2.5, $item->getPrice());
    }

    public function testToArray(): void
    {
        $item = new Item('p1', 2, 10.0);
        $arr = $item->toArray();

        $this->assertSame(['product_id' => 'p1', 'quantity' => 2, 'price' => 10.0], $arr);
    }
}

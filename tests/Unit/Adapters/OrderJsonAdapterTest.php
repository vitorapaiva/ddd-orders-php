<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Adapters;

use Orders\Adapters\Inbound\OrderJsonAdapter;
use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

class OrderJsonAdapterTest extends TestCase
{
    private OrderJsonAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new OrderJsonAdapter();
    }

    public function testToOrderDataReturnsCorrectStructure(): void
    {
        $json = TestHelpers::validOrderData();
        $data = $this->adapter->toOrderData($json);

        $this->assertSame('cust-1', $data['customer_id']);
        $this->assertInstanceOf(\Orders\Domain\ValueObjects\Address::class, $data['shipping_address']);
        $this->assertInstanceOf(\Orders\Domain\ValueObjects\Address::class, $data['billing_address']);
        $this->assertCount(1, $data['items']);
        $this->assertInstanceOf(\Orders\Domain\ValueObjects\Item::class, $data['items'][0]);
    }

    public function testThrowsWhenCustomerIdMissing(): void
    {
        $json = TestHelpers::validOrderData();
        unset($json['customer_id']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('customer_id');

        $this->adapter->toOrderData($json);
    }

    public function testThrowsWhenItemsEmpty(): void
    {
        $json = TestHelpers::validOrderData();
        $json['items'] = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('non-empty array');

        $this->adapter->toOrderData($json);
    }

    public function testThrowsWhenShippingAddressNotObject(): void
    {
        $json = TestHelpers::validOrderData();
        $json['shipping_address'] = 'invalid';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('shipping_address must be an object');

        $this->adapter->toOrderData($json);
    }
}

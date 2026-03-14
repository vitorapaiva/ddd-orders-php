<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Adapters;

use Orders\Adapters\Outbound\ProductsAdapter;
use PHPUnit\Framework\TestCase;

class ProductsAdapterTest extends TestCase
{
    public function testItemsToRequest(): void
    {
        $adapter = new ProductsAdapter();
        $items = [['product_id' => 'p1', 'quantity' => 2]];

        $request = $adapter->itemsToRequest('order-123', $items);

        $this->assertSame('order-123', $request['order_id']);
        $this->assertSame($items, $request['items']);
    }

    public function testResponseToResultSuccess(): void
    {
        $adapter = new ProductsAdapter();

        $result = $adapter->responseToResult(200, ['products' => []]);

        $this->assertTrue($result['success']);
        $this->assertSame([], $result['products']);
    }

    public function testResponseToResultFailure(): void
    {
        $adapter = new ProductsAdapter();

        $result = $adapter->responseToResult(404, ['error' => 'Not found']);

        $this->assertFalse($result['success']);
        $this->assertSame('Not found', $result['error']);
    }
}

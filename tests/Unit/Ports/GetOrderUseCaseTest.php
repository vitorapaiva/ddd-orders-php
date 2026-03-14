<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Ports;

use Orders\Domain\Exceptions\OrderNotFoundException;
use Orders\Ports\Inbound\GetOrderUseCase;
use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

class GetOrderUseCaseTest extends TestCase
{
    public function testExecuteReturnsOrderWhenFound(): void
    {
        $repo = TestHelpers::createInMemoryRepository();
        $order = TestHelpers::validOrder();
        $repo->save($order);
        $orderId = $order->toDto()->id;

        $useCase = new GetOrderUseCase($repo);
        $result = $useCase->execute($orderId);

        $this->assertSame($orderId, $result->toDto()->id);
    }

    public function testExecuteThrowsWhenNotFound(): void
    {
        $repo = TestHelpers::createInMemoryRepository();
        $useCase = new GetOrderUseCase($repo);

        $this->expectException(OrderNotFoundException::class);

        $useCase->execute('non-existent-id');
    }
}

<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Ports;

use Orders\Domain\Exceptions\OrderNotFoundException;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Ports\Inbound\UpdateOrderStatusUseCase;
use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

class UpdateOrderStatusUseCaseTest extends TestCase
{
    public function testExecuteUpdatesStatus(): void
    {
        $repo = TestHelpers::createInMemoryRepository();
        $order = TestHelpers::validOrder();
        $order->updateStatus(OrderStatus::PRODUCTS_RESERVED);
        $repo->save($order);
        $orderId = $order->toDto()->id;

        $publisher = TestHelpers::createMockEventPublisher();
        $useCase = new UpdateOrderStatusUseCase($repo, $publisher);

        $result = $useCase->execute($orderId, 'payment_processed');

        $this->assertSame(OrderStatus::PAYMENT_PROCESSED, $result->getStatus());
    }

    public function testExecuteThrowsWhenNotFound(): void
    {
        $repo = TestHelpers::createInMemoryRepository();
        $publisher = TestHelpers::createMockEventPublisher();
        $useCase = new UpdateOrderStatusUseCase($repo, $publisher);

        $this->expectException(OrderNotFoundException::class);

        $useCase->execute('non-existent', 'payment_processed');
    }
}

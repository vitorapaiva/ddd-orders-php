<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Ports;

use Orders\Ports\Inbound\CloseOrderUseCase;
use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

class CloseOrderUseCaseTest extends TestCase
{
    public function testExecuteCreatesOrderAndSaves(): void
    {
        $repo = TestHelpers::createInMemoryRepository();
        $productsService = TestHelpers::createMockProductsService(true);
        $orderCreatedHandler = new \Orders\Infra\EventHandlers\OrderCreatedHandler($productsService);
        $orderUpdatedHandler = new \Orders\Infra\EventHandlers\OrderUpdatedHandler();
        $logger = new \Monolog\Logger('test');
        $logger->pushHandler(new \Monolog\Handler\NullHandler());
        $publisher = new \Orders\Infra\EventPublisher($logger, $orderCreatedHandler, $orderUpdatedHandler);

        $useCase = new CloseOrderUseCase($repo, $publisher);
        $addr = TestHelpers::validAddress();
        $orderData = [
            'customer_id' => 'cust-1',
            'shipping_address' => $addr,
            'billing_address' => $addr,
            'items' => [\Orders\Domain\ValueObjects\Item::fromArray(['product_id' => 'p1', 'quantity' => 2, 'price' => 10.0])],
        ];

        $order = $useCase->execute($orderData);

        $this->assertNotEmpty($order->toDto()->id);
        $this->assertSame(\Orders\Domain\ValueObjects\OrderStatus::PRODUCTS_RESERVED, $order->getStatus());
        $this->assertNotNull($repo->findById($order->toDto()->id));
    }
}

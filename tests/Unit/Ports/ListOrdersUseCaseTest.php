<?php

declare(strict_types=1);

namespace Orders\Tests\Unit\Ports;

use Orders\Ports\Inbound\ListOrdersUseCase;
use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

class ListOrdersUseCaseTest extends TestCase
{
    public function testExecuteReturnsEmptyWhenNoOrders(): void
    {
        $repo = TestHelpers::createInMemoryRepository();
        $useCase = new ListOrdersUseCase($repo);

        $orders = $useCase->execute();

        $this->assertEmpty($orders);
    }

    public function testExecuteReturnsAllOrders(): void
    {
        $repo = TestHelpers::createInMemoryRepository();
        $repo->save(TestHelpers::validOrder());
        $useCase = new ListOrdersUseCase($repo);

        $orders = $useCase->execute();

        $this->assertCount(1, $orders);
    }
}

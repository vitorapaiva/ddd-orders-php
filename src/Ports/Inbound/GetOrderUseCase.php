<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Domain\Entities\Order;
use Orders\Ports\Outbound\OrderRepositoryInterface;

class GetOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository
    ) {}

    public function execute(string $orderId): Order
    {
        $order = $this->repository->findById($orderId);

        if ($order === null) {
            throw new \DomainException('Order not found');
        }

        return $order;
    }
}

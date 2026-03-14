<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Domain\Entities\Order;
use Orders\Domain\Exceptions\OrderNotFoundException;
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
            throw new OrderNotFoundException($orderId);
        }

        return $order;
    }
}

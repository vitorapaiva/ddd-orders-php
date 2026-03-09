<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Ports\Outbound\OrderRepositoryInterface;

class GetOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository
    ) {}

    public function execute(string $orderId): array
    {
        $order = $this->repository->findById($orderId);

        if ($order === null) {
            return [
                'success' => false,
                'order' => null,
                'error' => 'Order not found',
            ];
        }

        return [
            'success' => true,
            'order' => $order,
            'error' => null,
        ];
    }
}

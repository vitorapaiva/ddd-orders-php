<?php

declare(strict_types=1);

namespace Orders\Infra\EventHandlers;

use Orders\Domain\Events\OrderCreated;
use Orders\Ports\Outbound\ProductsServiceInterface;

class OrderCreatedHandler implements EventHandlerInterface
{
    public function __construct(
        private readonly ProductsServiceInterface $productsService
    ) {}

    public function supports(object $event): bool
    {
        return $event instanceof OrderCreated;
    }

    public function handle(object $event): void
    {
        if (!$event instanceof OrderCreated) {
            return;
        }

        $orderId = $event->data['order_id'];
        $items = $event->data['items'];

        $result = $this->productsService->reserveProducts($orderId, $items);

        if (!$result['success']) {
            throw new \DomainException(
                $result['error'] ?? 'Failed to reserve products'
            );
        }
    }
}

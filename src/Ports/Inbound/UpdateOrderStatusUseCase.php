<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Domain\Entities\Order;
use Orders\Domain\Events\OrderUpdated;
use Orders\Domain\Exceptions\OrderNotFoundException;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Ports\Outbound\EventPublisherInterface;

class UpdateOrderStatusUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher
    ) {}

    public function execute(string $orderId, string $newStatus): Order
    {
        $order = $this->repository->findById($orderId);

        if ($order === null) {
            throw new OrderNotFoundException($orderId);
        }

        $previousStatus = $order->getStatus();
        $newStatusEnum = OrderStatus::from($newStatus);

        $order->updateStatus($newStatusEnum);
        $this->repository->update($order);

        $this->eventPublisher->publish(
            new OrderUpdated($order, $previousStatus)
        );

        return $order;
    }
}

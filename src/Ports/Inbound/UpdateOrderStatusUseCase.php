<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Domain\Events\OrderUpdated;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Ports\Outbound\EventPublisherInterface;

class UpdateOrderStatusUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher
    ) {}

    public function execute(string $orderId, string $newStatus): array
    {
        try {
            $order = $this->repository->findById($orderId);

            if ($order === null) {
                return [
                    'success' => false,
                    'order' => null,
                    'error' => 'Order not found',
                ];
            }

            $previousStatus = $order->getStatus();
            $newStatusEnum = OrderStatus::from($newStatus);

            $order->updateStatus($newStatusEnum);
            $this->repository->update($order);

            $this->eventPublisher->publish(
                new OrderUpdated($order, $previousStatus)
            );

            return [
                'success' => true,
                'order' => $order,
                'error' => null,
            ];
        } catch (\ValueError $e) {
            return [
                'success' => false,
                'order' => null,
                'error' => 'Invalid status',
            ];
        } catch (\DomainException $e) {
            return [
                'success' => false,
                'order' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}

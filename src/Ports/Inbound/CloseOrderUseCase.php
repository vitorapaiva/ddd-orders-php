<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Domain\Entities\Order;
use Orders\Domain\Events\OrderCreated;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Ports\Outbound\ProductsServiceInterface;
use Orders\Ports\Outbound\EventPublisherInterface;

class CloseOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository,
        private readonly ProductsServiceInterface $productsService,
        private readonly EventPublisherInterface $eventPublisher
    ) {}

    public function execute(array $orderData): array
    {
        try {
            $order = new Order(
                customerId: $orderData['customer_id'],
                shippingAddress: $orderData['shipping_address'],
                billingAddress: $orderData['billing_address'],
                items: $orderData['items']
            );

            $this->repository->save($order);

            $itemsToReserve = $order->getProductsForReservation();
            $reservationResult = $this->productsService->reserveProducts(
                $order->getId(),
                $itemsToReserve
            );

            if (!$reservationResult['success']) {
                return [
                    'success' => false,
                    'order' => null,
                    'error' => $reservationResult['error'] ?? 'Failed to reserve products',
                ];
            }

            $order->updateStatus(OrderStatus::PRODUCTS_RESERVED);
            $this->repository->update($order);

            $this->eventPublisher->publish(new OrderCreated($order));

            return [
                'success' => true,
                'order' => $order,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'order' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}

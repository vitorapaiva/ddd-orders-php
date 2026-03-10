<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Domain\Entities\Order;
use Orders\Domain\Events\OrderCreated;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Ports\Outbound\EventPublisherInterface;

class CloseOrderUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository,
        private readonly EventPublisherInterface $eventPublisher
    ) {}

    public function execute(array $orderData): Order
    {
        $order = new Order(
            customerId: $orderData['customer_id'],
            shippingAddress: $orderData['shipping_address'],
            billingAddress: $orderData['billing_address'],
            items: $orderData['items']
        );

        $this->repository->save($order);

        $this->eventPublisher->publish(new OrderCreated($order));

        $order->updateStatus(OrderStatus::PRODUCTS_RESERVED);
        $this->repository->update($order);

        return $order;
    }
}

<?php

declare(strict_types=1);

namespace Orders\Domain\Entities;

use Orders\Domain\DTO\OrderDto;
use Orders\Domain\ValueObjects\Address;
use Orders\Domain\ValueObjects\Item;
use Orders\Domain\ValueObjects\OrderStatus;
use Orders\Domain\ValueObjects\OrderStatusTransitions;
use Ramsey\Uuid\Uuid;

class Order
{
    private string $id;
    private string $customerId;
    private Address $shippingAddress;
    private Address $billingAddress;
    private array $items;
    private float $total;
    private OrderStatus $status;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $customerId,
        Address $shippingAddress,
        Address $billingAddress,
        array $items,
        ?string $id = null,
        ?OrderStatus $status = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null
    ) {
        if (trim($customerId) === '') {
            throw new \InvalidArgumentException('Customer ID is required');
        }
        if (empty($items)) {
            throw new \InvalidArgumentException('Order must contain at least one item');
        }

        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->customerId = $customerId;
        $this->shippingAddress = $shippingAddress;
        $this->billingAddress = $billingAddress;
        $this->items = $items;
        $this->total = $this->calculateTotal();
        $this->status = $status ?? OrderStatus::PENDING_PAYMENT;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
    }

    private function calculateTotal(): float
    {
        return array_reduce(
            $this->items,
            fn(float $total, Item $item) => $total + $item->calculateSubtotal(),
            0.0
        );
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function updateStatus(OrderStatus $newStatus): void
    {
        if (!OrderStatusTransitions::canTransition($this->status, $newStatus)) {
            throw new \DomainException(
                "Invalid status transition: {$this->status->value} -> {$newStatus->value}"
            );
        }

        $this->status = $newStatus;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function toDto(): OrderDto
    {
        return new OrderDto(
            id: $this->id,
            customerId: $this->customerId,
            shippingAddress: $this->shippingAddress->toArray(),
            billingAddress: $this->billingAddress->toArray(),
            items: array_map(fn(Item $item) => $item->toArray(), $this->items),
            total: $this->total,
            status: $this->status->value,
            createdAt: $this->createdAt->format('Y-m-d H:i:s'),
            updatedAt: $this->updatedAt->format('Y-m-d H:i:s'),
        );
    }
}

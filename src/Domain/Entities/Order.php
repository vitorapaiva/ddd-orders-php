<?php

declare(strict_types=1);

namespace Orders\Domain\Entities;

use Orders\Domain\ValueObjects\Address;
use Orders\Domain\ValueObjects\Item;
use Orders\Domain\ValueObjects\OrderStatus;
use Ramsey\Uuid\Uuid;

class Order
{
    private const ALLOWED_TRANSITIONS = [
        'pending_payment' => ['products_reserved', 'cancelled'],
        'products_reserved' => ['payment_processed', 'cancelled'],
        'payment_processed' => ['products_picked', 'cancelled'],
        'products_picked' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

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

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getShippingAddress(): Address
    {
        return $this->shippingAddress;
    }

    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function canTransitionTo(OrderStatus $newStatus): bool
    {
        $allowedTransitions = self::ALLOWED_TRANSITIONS[$this->status->value] ?? [];
        return in_array($newStatus->value, $allowedTransitions, true);
    }

    public function updateStatus(OrderStatus $newStatus): void
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \DomainException(
                "Invalid status transition: {$this->status->value} -> {$newStatus->value}"
            );
        }

        $this->status = $newStatus;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getProductsForReservation(): array
    {
        return array_map(
            fn(Item $item) => [
                'product_id' => $item->getProductId(),
                'quantity' => $item->getQuantity(),
            ],
            $this->items
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customerId,
            'shipping_address' => $this->shippingAddress->toArray(),
            'billing_address' => $this->billingAddress->toArray(),
            'items' => array_map(fn(Item $item) => $item->toArray(), $this->items),
            'total' => $this->total,
            'status' => $this->status->value,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}

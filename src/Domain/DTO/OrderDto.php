<?php

declare(strict_types=1);

namespace Orders\Domain\DTO;

final readonly class OrderDto
{
    public function __construct(
        public string $id,
        public string $customerId,
        public array $shippingAddress,
        public array $billingAddress,
        public array $items,
        public float $total,
        public string $status,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customerId,
            'shipping_address' => $this->shippingAddress,
            'billing_address' => $this->billingAddress,
            'items' => $this->items,
            'total' => $this->total,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function toEventPayload(): array
    {
        return [
            'order_id' => $this->id,
            'customer_id' => $this->customerId,
            'items' => $this->items,
            'total' => $this->total,
            'shipping_address' => $this->shippingAddress,
            'billing_address' => $this->billingAddress,
        ];
    }
}

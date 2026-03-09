<?php

declare(strict_types=1);

namespace Orders\Domain\ValueObjects;

final class Item
{
    public function __construct(
        private readonly string $productId,
        private readonly int $quantity,
        private readonly float $price
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }
        if ($price <= 0) {
            throw new \InvalidArgumentException('Price must be greater than zero');
        }
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function calculateSubtotal(): float
    {
        return $this->quantity * $this->price;
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            quantity: (int) $data['quantity'],
            price: (float) $data['price']
        );
    }
}

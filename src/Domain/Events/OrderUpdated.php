<?php

declare(strict_types=1);

namespace Orders\Domain\Events;

use Orders\Domain\Entities\Order;
use Orders\Domain\ValueObjects\OrderStatus;

final class OrderUpdated
{
    public readonly string $type;
    public readonly array $data;
    public readonly \DateTimeImmutable $timestamp;

    public function __construct(Order $order, OrderStatus $previousStatus)
    {
        $this->type = 'order_updated';
        $dto = $order->toDto();
        $this->data = [
            'order_id' => $dto->id,
            'previous_status' => $previousStatus->value,
            'current_status' => $dto->status,
        ];
        $this->timestamp = new \DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => $this->data,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
        ];
    }
}

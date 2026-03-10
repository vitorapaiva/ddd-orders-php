<?php

declare(strict_types=1);

namespace Orders\Domain\Events;

use Orders\Domain\Entities\Order;

final class OrderCreated
{
    public readonly string $type;
    public readonly array $data;
    public readonly \DateTimeImmutable $timestamp;

    public function __construct(Order $order)
    {
        $this->type = 'order_created';
        $this->data = $order->toDto()->toEventPayload();
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

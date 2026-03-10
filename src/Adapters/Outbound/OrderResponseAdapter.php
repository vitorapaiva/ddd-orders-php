<?php

declare(strict_types=1);

namespace Orders\Adapters\Outbound;

use Orders\Domain\Entities\Order;

class OrderResponseAdapter implements OrderResponseAdapterInterface
{
    public function toJson(Order $order): array
    {
        return $order->toDto()->toArray();
    }

    public function toJsonList(array $orders): array
    {
        return array_map(
            fn(Order $order) => $this->toJson($order),
            $orders
        );
    }
}

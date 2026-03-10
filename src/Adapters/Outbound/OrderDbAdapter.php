<?php

declare(strict_types=1);

namespace Orders\Adapters\Outbound;

use Orders\Domain\Entities\Order;

class OrderDbAdapter implements OrderDbAdapterInterface
{
    public function toDb(Order $order): array
    {
        $dto = $order->toDto();
        $data = $dto->toArray();

        return [
            'id' => $data['id'],
            'customer_id' => $data['customer_id'],
            'shipping_address' => json_encode($data['shipping_address']),
            'billing_address' => json_encode($data['billing_address']),
            'items' => json_encode($data['items']),
            'total' => $data['total'],
            'status' => $data['status'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at'],
        ];
    }
}

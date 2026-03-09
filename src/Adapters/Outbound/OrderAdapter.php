<?php

declare(strict_types=1);

namespace Orders\Adapters\Outbound;

use Orders\Domain\Entities\Order;
use Orders\Domain\ValueObjects\Item;

class OrderAdapter
{
    public static function orderToDb(Order $order): array
    {
        return [
            'id' => $order->getId(),
            'customer_id' => $order->getCustomerId(),
            'shipping_address' => json_encode($order->getShippingAddress()->toArray()),
            'billing_address' => json_encode($order->getBillingAddress()->toArray()),
            'items' => json_encode(array_map(fn(Item $item) => $item->toArray(), $order->getItems())),
            'total' => $order->getTotal(),
            'status' => $order->getStatus()->value,
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    public static function orderToJson(Order $order): array
    {
        return $order->toArray();
    }

    public static function ordersToJson(array $orders): array
    {
        return array_map(
            fn(Order $order) => self::orderToJson($order),
            $orders
        );
    }
}

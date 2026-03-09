<?php

declare(strict_types=1);

namespace Orders\Adapters\Inbound;

use Orders\Domain\Entities\Order;
use Orders\Domain\ValueObjects\Address;
use Orders\Domain\ValueObjects\Item;
use Orders\Domain\ValueObjects\OrderStatus;

class OrderAdapter
{
    public static function jsonToOrderData(array $json): array
    {
        return [
            'customer_id' => $json['customer_id'],
            'shipping_address' => Address::fromArray($json['shipping_address']),
            'billing_address' => Address::fromArray($json['billing_address']),
            'items' => array_map(
                fn(array $item) => Item::fromArray($item),
                $json['items']
            ),
        ];
    }

    public static function dbToOrder(array $row): Order
    {
        $shippingAddressData = json_decode($row['shipping_address'], true);
        $billingAddressData = json_decode($row['billing_address'], true);
        $itemsData = json_decode($row['items'], true);

        return new Order(
            customerId: $row['customer_id'],
            shippingAddress: Address::fromArray($shippingAddressData),
            billingAddress: Address::fromArray($billingAddressData),
            items: array_map(fn(array $item) => Item::fromArray($item), $itemsData),
            id: $row['id'],
            status: OrderStatus::from($row['status']),
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at'])
        );
    }
}

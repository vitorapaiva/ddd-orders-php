<?php

declare(strict_types=1);

namespace Orders\Adapters\Inbound;

use Orders\Domain\ValueObjects\Address;
use Orders\Domain\ValueObjects\Item;

class OrderJsonAdapter implements OrderJsonAdapterInterface
{
    public function toOrderData(array $json): array
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
}

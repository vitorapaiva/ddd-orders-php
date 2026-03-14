<?php

declare(strict_types=1);

namespace Orders\Adapters\Inbound;

use Orders\Domain\Validation\ValidationHelper;
use Orders\Domain\ValueObjects\Address;
use Orders\Domain\ValueObjects\Item;

class OrderJsonAdapter implements OrderJsonAdapterInterface
{
    public function toOrderData(array $json): array
    {
        ValidationHelper::requireKeys($json, ['customer_id', 'shipping_address', 'billing_address', 'items'], 'Request body');
        ValidationHelper::requireNonEmptyArray($json, 'items', 'Items must be a non-empty array');
        ValidationHelper::requireArray($json, 'shipping_address', 'shipping_address must be an object');
        ValidationHelper::requireArray($json, 'billing_address', 'billing_address must be an object');

        return [
            'customer_id' => (string) $json['customer_id'],
            'shipping_address' => Address::fromArray($json['shipping_address']),
            'billing_address' => Address::fromArray($json['billing_address']),
            'items' => array_map(
                fn(array $item) => Item::fromArray($item),
                $json['items']
            ),
        ];
    }
}

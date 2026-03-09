<?php

declare(strict_types=1);

namespace Orders\Adapters\Outbound;

class ProductsAdapter
{
    public static function itemsToRequest(string $orderId, array $items): array
    {
        return [
            'order_id' => $orderId,
            'items' => $items,
        ];
    }

    public static function responseToResult(int $status, ?array $body): array
    {
        if ($status === 200) {
            return [
                'success' => true,
                'products' => $body['products'] ?? [],
                'error' => null,
            ];
        }

        return [
            'success' => false,
            'products' => null,
            'error' => $body['error'] ?? 'Failed to reserve products',
        ];
    }
}

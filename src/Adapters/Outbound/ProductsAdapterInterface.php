<?php

declare(strict_types=1);

namespace Orders\Adapters\Outbound;

interface ProductsAdapterInterface
{
    public function itemsToRequest(string $orderId, array $items): array;
    
    public function responseToResult(int $status, ?array $body): array;
}

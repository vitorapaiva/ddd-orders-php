<?php

declare(strict_types=1);

namespace Orders\Ports\Outbound;

interface ProductsServiceInterface
{
    public function reserveProducts(string $orderId, array $items): array;
    public function releaseReservation(string $orderId): array;
}

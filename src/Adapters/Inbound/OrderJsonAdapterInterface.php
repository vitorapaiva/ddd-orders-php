<?php

declare(strict_types=1);

namespace Orders\Adapters\Inbound;

interface OrderJsonAdapterInterface
{
    public function toOrderData(array $json): array;
}

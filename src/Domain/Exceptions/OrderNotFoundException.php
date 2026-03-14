<?php

declare(strict_types=1);

namespace Orders\Domain\Exceptions;

final class OrderNotFoundException extends \DomainException
{
    public function __construct(string $orderId)
    {
        parent::__construct("Order not found");
    }
}

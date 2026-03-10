<?php

declare(strict_types=1);

namespace Orders\Ports\Inbound;

use Orders\Ports\Outbound\OrderRepositoryInterface;

class ListOrdersUseCase
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->listAll();
    }
}

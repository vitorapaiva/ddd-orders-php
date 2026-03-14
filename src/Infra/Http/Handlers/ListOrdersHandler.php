<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Infra\Http\JsonResponseHelper;
use Orders\Ports\Inbound\ListOrdersUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListOrdersHandler
{
    public function __construct(
        private readonly ListOrdersUseCase $useCase
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $orders = $this->useCase->execute();

        return JsonResponseHelper::success($response, [
            'orders' => array_map(fn($o) => $o->toDto()->toArray(), $orders),
        ]);
    }
}

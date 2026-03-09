<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Outbound\OrderAdapter as OutboundAdapter;
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
        $result = $this->useCase->execute();

        $response->getBody()->write(json_encode([
            'orders' => OutboundAdapter::ordersToJson($result['orders']),
        ]));

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }
}

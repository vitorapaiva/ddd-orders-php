<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Outbound\OrderAdapter as OutboundAdapter;
use Orders\Ports\Inbound\GetOrderUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetOrderHandler
{
    public function __construct(
        private readonly GetOrderUseCase $useCase
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $orderId = $args['id'];

        $result = $this->useCase->execute($orderId);

        if ($result['success']) {
            $response->getBody()->write(json_encode([
                'order' => OutboundAdapter::orderToJson($result['order']),
            ]));

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'error' => $result['error'],
        ]));

        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json');
    }
}

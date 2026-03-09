<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Inbound\OrderAdapter as InboundAdapter;
use Orders\Adapters\Outbound\OrderAdapter as OutboundAdapter;
use Orders\Ports\Inbound\CloseOrderUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CloseOrderHandler
{
    public function __construct(
        private readonly CloseOrderUseCase $useCase
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $orderData = InboundAdapter::jsonToOrderData($body);

        $result = $this->useCase->execute($orderData);

        if ($result['success']) {
            $response->getBody()->write(json_encode([
                'message' => 'Order created successfully',
                'order' => OutboundAdapter::orderToJson($result['order']),
            ]));

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'error' => $result['error'],
        ]));

        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }
}

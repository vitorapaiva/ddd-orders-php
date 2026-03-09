<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Outbound\OrderAdapter as OutboundAdapter;
use Orders\Ports\Inbound\UpdateOrderStatusUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateStatusHandler
{
    public function __construct(
        private readonly UpdateOrderStatusUseCase $useCase
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $orderId = $args['id'];
        $body = $request->getParsedBody();
        $newStatus = $body['status'] ?? '';

        $result = $this->useCase->execute($orderId, $newStatus);

        if ($result['success']) {
            $response->getBody()->write(json_encode([
                'message' => 'Status updated',
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
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }
}

<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Infra\Http\JsonResponseHelper;
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
        $body = $request->getParsedBody() ?? [];
        $newStatus = $body['status'] ?? '';

        if (trim($newStatus) === '') {
            throw new \InvalidArgumentException('Status is required');
        }

        $order = $this->useCase->execute($orderId, $newStatus);

        return JsonResponseHelper::success($response, [
            'message' => 'Status updated',
            'order' => $order->toDto()->toArray(),
        ]);
    }
}

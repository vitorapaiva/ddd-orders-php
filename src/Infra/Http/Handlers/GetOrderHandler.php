<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Infra\Http\JsonResponseHelper;
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
        $order = $this->useCase->execute($orderId);

        return JsonResponseHelper::success($response, [
            'order' => $order->toDto()->toArray(),
        ]);
    }
}

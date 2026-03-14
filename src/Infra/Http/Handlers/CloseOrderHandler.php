<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Inbound\OrderJsonAdapterInterface;
use Orders\Infra\Http\JsonResponseHelper;
use Orders\Ports\Inbound\CloseOrderUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CloseOrderHandler
{
    public function __construct(
        private readonly CloseOrderUseCase $useCase,
        private readonly OrderJsonAdapterInterface $jsonAdapter
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? [];
        $orderData = $this->jsonAdapter->toOrderData($body);
        $order = $this->useCase->execute($orderData);

        return JsonResponseHelper::success($response, [
            'message' => 'Order created successfully',
            'order' => $order->toDto()->toArray(),
        ], 201);
    }
}

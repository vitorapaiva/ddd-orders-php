<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Inbound\OrderJsonAdapterInterface;
use Orders\Adapters\Outbound\OrderResponseAdapterInterface;
use Orders\Infra\Http\HandlerExceptionResolver;
use Orders\Infra\Http\JsonResponseHelper;
use Orders\Ports\Inbound\CloseOrderUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CloseOrderHandler
{
    public function __construct(
        private readonly CloseOrderUseCase $useCase,
        private readonly OrderJsonAdapterInterface $jsonAdapter,
        private readonly OrderResponseAdapterInterface $responseAdapter
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $body = $request->getParsedBody();
            $orderData = $this->jsonAdapter->toOrderData($body);
            $order = $this->useCase->execute($orderData);

            return JsonResponseHelper::success($response, [
                'message' => 'Order created successfully',
                'order' => $this->responseAdapter->toJson($order),
            ], 201);
        } catch (\Throwable $e) {
            return HandlerExceptionResolver::resolve($e, $response);
        }
    }
}

<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Outbound\OrderResponseAdapterInterface;
use Orders\Infra\Http\HandlerExceptionResolver;
use Orders\Infra\Http\JsonResponseHelper;
use Orders\Ports\Inbound\GetOrderUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetOrderHandler
{
    public function __construct(
        private readonly GetOrderUseCase $useCase,
        private readonly OrderResponseAdapterInterface $responseAdapter
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $orderId = $args['id'];
            $order = $this->useCase->execute($orderId);

            return JsonResponseHelper::success($response, [
                'order' => $this->responseAdapter->toJson($order),
            ]);
        } catch (\Throwable $e) {
            return HandlerExceptionResolver::resolve($e, $response);
        }
    }
}

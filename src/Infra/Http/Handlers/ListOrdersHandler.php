<?php

declare(strict_types=1);

namespace Orders\Infra\Http\Handlers;

use Orders\Adapters\Outbound\OrderResponseAdapterInterface;
use Orders\Infra\Http\HandlerExceptionResolver;
use Orders\Infra\Http\JsonResponseHelper;
use Orders\Ports\Inbound\ListOrdersUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListOrdersHandler
{
    public function __construct(
        private readonly ListOrdersUseCase $useCase,
        private readonly OrderResponseAdapterInterface $responseAdapter
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $orders = $this->useCase->execute();

            return JsonResponseHelper::success($response, [
                'orders' => $this->responseAdapter->toJsonList($orders),
            ]);
        } catch (\Throwable $e) {
            return HandlerExceptionResolver::resolve($e, $response);
        }
    }
}

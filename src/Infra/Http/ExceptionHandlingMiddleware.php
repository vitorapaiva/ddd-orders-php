<?php

declare(strict_types=1);

namespace Orders\Infra\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class ExceptionHandlingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory
    ) {}

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            $response = $this->responseFactory->createResponse();
            return HandlerExceptionResolver::resolve($e, $response);
        }
    }
}

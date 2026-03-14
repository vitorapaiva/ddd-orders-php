<?php

declare(strict_types=1);

namespace Orders\Infra\Http;

use Orders\Domain\Exceptions\OrderNotFoundException;
use Psr\Http\Message\ResponseInterface;

final class HandlerExceptionResolver
{
    public static function resolve(\Throwable $e, ResponseInterface $response): ResponseInterface
    {
        if ($e instanceof OrderNotFoundException) {
            return JsonResponseHelper::error($response, $e->getMessage(), 404);
        }
        if ($e instanceof \ValueError || $e instanceof \InvalidArgumentException) {
            return JsonResponseHelper::error($response, $e->getMessage(), 400);
        }
        if ($e instanceof \DomainException) {
            return JsonResponseHelper::error($response, $e->getMessage(), 400);
        }
        return JsonResponseHelper::error($response, 'Internal server error', 500);
    }
}

<?php

declare(strict_types=1);

namespace Orders\Infra\Http;

use Psr\Http\Message\ResponseInterface;

final class HandlerExceptionResolver
{
    public static function resolve(\Throwable $e, ResponseInterface $response): ResponseInterface
    {
        if ($e instanceof \ValueError) {
            return JsonResponseHelper::error($response, 'Invalid status', 400);
        }
        if ($e instanceof \DomainException) {
            $status = str_contains($e->getMessage(), 'not found') ? 404 : 400;
            return JsonResponseHelper::error($response, $e->getMessage(), $status);
        }
        return JsonResponseHelper::error($response, 'Internal server error', 500);
    }
}

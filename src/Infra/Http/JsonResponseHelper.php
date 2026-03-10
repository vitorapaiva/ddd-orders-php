<?php

declare(strict_types=1);

namespace Orders\Infra\Http;

use Psr\Http\Message\ResponseInterface;

final class JsonResponseHelper
{
    public static function success(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public static function error(ResponseInterface $response, string $message, int $status = 400): ResponseInterface
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }
}

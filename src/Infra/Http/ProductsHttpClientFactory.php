<?php

declare(strict_types=1);

namespace Orders\Infra\Http;

use GuzzleHttp\Client;

final class ProductsHttpClientFactory
{
    public static function create(string $baseUrl): Client
    {
        return new Client([
            'base_uri' => $baseUrl,
            'timeout' => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }
}

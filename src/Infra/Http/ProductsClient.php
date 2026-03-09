<?php

declare(strict_types=1);

namespace Orders\Infra\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Orders\Ports\Outbound\ProductsServiceInterface;
use Orders\Adapters\Outbound\ProductsAdapter;

class ProductsClient implements ProductsServiceInterface
{
    private Client $client;

    public function __construct(string $baseUrl)
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function reserveProducts(string $orderId, array $items): array
    {
        try {
            $requestBody = ProductsAdapter::itemsToRequest($orderId, $items);

            $response = $this->client->post('/products/reserve', [
                'json' => $requestBody,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return ProductsAdapter::responseToResult(
                $response->getStatusCode(),
                $body
            );
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'products' => null,
                'error' => 'Communication error: ' . $e->getMessage(),
            ];
        }
    }

    public function releaseReservation(string $orderId): array
    {
        try {
            $response = $this->client->delete("/products/reserve/{$orderId}");

            return ProductsAdapter::responseToResult(
                $response->getStatusCode(),
                null
            );
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => 'Communication error: ' . $e->getMessage(),
            ];
        }
    }
}

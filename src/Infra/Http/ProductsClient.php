<?php

declare(strict_types=1);

namespace Orders\Infra\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Orders\Adapters\Outbound\ProductsAdapterInterface;
use Orders\Ports\Outbound\ProductsServiceInterface;

class ProductsClient implements ProductsServiceInterface
{
    public function __construct(
        private readonly Client $httpClient,
        private readonly ProductsAdapterInterface $productsAdapter
    ) {}

    public function reserveProducts(string $orderId, array $items): array
    {
        try {
            $requestBody = $this->productsAdapter->itemsToRequest($orderId, $items);

            $response = $this->httpClient->post('/products/reserve', [
                'json' => $requestBody,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return $this->productsAdapter->responseToResult(
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
            $response = $this->httpClient->delete("/products/reserve/{$orderId}");

            return $this->productsAdapter->responseToResult(
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

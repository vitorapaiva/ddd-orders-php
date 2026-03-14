<?php

declare(strict_types=1);

namespace Orders\Tests\Integration;

use Orders\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;

class ApiTest extends TestCase
{
    private \Slim\App $app;

    protected function setUp(): void
    {
        $this->app = TestAppFactory::create();
    }

    public function testPostOrdersCloseCreatesOrder(): void
    {
        $body = json_encode(TestHelpers::validOrderData());
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/orders/close')
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream($body));

        $response = $this->app->handle($request);

        $this->assertSame(201, $response->getStatusCode());
        $data = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('order', $data);
        $this->assertArrayHasKey('id', $data['order']);
        $this->assertSame('Order created successfully', $data['message']);
    }

    public function testPostOrdersCloseInvalidBodyReturns400(): void
    {
        $body = json_encode(['customer_id' => null, 'items' => []]);
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/orders/close')
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream($body));

        $response = $this->app->handle($request);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testGetOrdersReturnsEmptyList(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/orders');
        $response = $this->app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('orders', $data);
        $this->assertEmpty($data['orders']);
    }

    public function testGetOrderByIdReturns404WhenNotFound(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/orders/non-existent-id');
        $response = $this->app->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testPutOrderStatusReturns404WhenNotFound(): void
    {
        $body = json_encode(['status' => 'payment_processed']);
        $request = (new ServerRequestFactory())->createServerRequest('PUT', '/orders/non-existent-id/status')
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream($body));

        $response = $this->app->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testFullFlowCreateListGetUpdate(): void
    {
        $body = json_encode(TestHelpers::validOrderData());
        $createRequest = (new ServerRequestFactory())->createServerRequest('POST', '/orders/close')
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream($body));

        $createResponse = $this->app->handle($createRequest);
        $this->assertSame(201, $createResponse->getStatusCode());

        $createData = json_decode((string) $createResponse->getBody(), true);
        $orderId = $createData['order']['id'];
        $this->assertNotEmpty($orderId);

        $listRequest = (new ServerRequestFactory())->createServerRequest('GET', '/orders');
        $listResponse = $this->app->handle($listRequest);
        $this->assertSame(200, $listResponse->getStatusCode());
        $listData = json_decode((string) $listResponse->getBody(), true);
        $this->assertCount(1, $listData['orders']);

        $getRequest = (new ServerRequestFactory())->createServerRequest('GET', "/orders/{$orderId}");
        $getResponse = $this->app->handle($getRequest);
        $this->assertSame(200, $getResponse->getStatusCode());
        $getData = json_decode((string) $getResponse->getBody(), true);
        $this->assertSame($orderId, $getData['order']['id']);

        $updateBody = json_encode(['status' => 'payment_processed']);
        $updateRequest = (new ServerRequestFactory())->createServerRequest('PUT', "/orders/{$orderId}/status")
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream($updateBody));
        $updateResponse = $this->app->handle($updateRequest);
        $this->assertSame(200, $updateResponse->getStatusCode());
        $updateData = json_decode((string) $updateResponse->getBody(), true);
        $this->assertSame('payment_processed', $updateData['order']['status']);
    }
}

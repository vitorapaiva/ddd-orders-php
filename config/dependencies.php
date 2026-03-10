<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Orders\Adapters\Inbound\OrderJsonAdapter;
use Orders\Adapters\Inbound\OrderJsonAdapterInterface;
use Orders\Adapters\Inbound\OrderDbAdapter as InboundOrderDbAdapter;
use Orders\Adapters\Inbound\OrderDbAdapterInterface as InboundOrderDbAdapterInterface;
use Orders\Adapters\Outbound\OrderDbAdapter as OutboundOrderDbAdapter;
use Orders\Adapters\Outbound\OrderDbAdapterInterface as OutboundOrderDbAdapterInterface;
use Orders\Adapters\Outbound\OrderResponseAdapter;
use Orders\Adapters\Outbound\OrderResponseAdapterInterface;
use Orders\Adapters\Outbound\ProductsAdapter;
use Orders\Adapters\Outbound\ProductsAdapterInterface;
use Orders\Infra\EventPublisher;
use Orders\Infra\EventHandlers\OrderCreatedHandler;
use Orders\Infra\EventHandlers\OrderUpdatedHandler;
use Orders\Infra\Http\ProductsClient;
use Orders\Infra\Http\ProductsHttpClientFactory;
use Orders\Infra\Persistence\Database;
use Orders\Infra\Persistence\DatabaseInterface;
use Orders\Infra\Persistence\OrderRepository;
use Orders\Ports\Outbound\EventPublisherInterface;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Ports\Outbound\ProductsServiceInterface;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'config' => [
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'database' => $_ENV['DB_DATABASE'] ?? 'orders_db',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? 'root',
            ],
            'products_service' => [
                'base_url' => $_ENV['PRODUCTS_SERVICE_URL'] ?? 'http://localhost:3001',
            ],
        ],

        DatabaseInterface::class => function (ContainerInterface $c) {
            $config = $c->get('config')['db'];
            $database = new Database($config);
            $database->createTables();
            return $database;
        },

        OrderJsonAdapterInterface::class => function () {
            return new OrderJsonAdapter();
        },

        InboundOrderDbAdapterInterface::class => function () {
            return new InboundOrderDbAdapter();
        },

        OutboundOrderDbAdapterInterface::class => function () {
            return new OutboundOrderDbAdapter();
        },

        OrderResponseAdapterInterface::class => function () {
            return new OrderResponseAdapter();
        },

        ProductsAdapterInterface::class => function () {
            return new ProductsAdapter();
        },

        OrderRepositoryInterface::class => function (ContainerInterface $c) {
            $database = $c->get(DatabaseInterface::class);
            $inboundDbAdapter = $c->get(InboundOrderDbAdapterInterface::class);
            $outboundDbAdapter = $c->get(OutboundOrderDbAdapterInterface::class);
            return new OrderRepository(
                $database->getPdo(),
                $inboundDbAdapter,
                $outboundDbAdapter
            );
        },

        ProductsServiceInterface::class => function (ContainerInterface $c) {
            $config = $c->get('config')['products_service'];
            $httpClient = ProductsHttpClientFactory::create($config['base_url']);
            $productsAdapter = $c->get(ProductsAdapterInterface::class);
            return new ProductsClient($httpClient, $productsAdapter);
        },

        OrderCreatedHandler::class => function (ContainerInterface $c) {
            $productsService = $c->get(ProductsServiceInterface::class);
            return new OrderCreatedHandler($productsService);
        },

        OrderUpdatedHandler::class => function () {
            return new OrderUpdatedHandler();
        },

        EventPublisherInterface::class => function (ContainerInterface $c) {
            return new EventPublisher(
                $c->get(OrderCreatedHandler::class),
                $c->get(OrderUpdatedHandler::class)
            );
        },
    ]);
};

<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Orders\Infra\EventPublisher;
use Orders\Infra\Http\ProductsClient;
use Orders\Infra\Persistence\Database;
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

        Database::class => function (ContainerInterface $c) {
            $config = $c->get('config')['db'];
            $database = new Database($config);
            $database->createTables();
            return $database;
        },

        OrderRepositoryInterface::class => function (ContainerInterface $c) {
            $database = $c->get(Database::class);
            return new OrderRepository($database->getPdo());
        },

        ProductsServiceInterface::class => function (ContainerInterface $c) {
            $config = $c->get('config')['products_service'];
            return new ProductsClient($config['base_url']);
        },

        EventPublisherInterface::class => function () {
            return new EventPublisher();
        },
    ]);
};

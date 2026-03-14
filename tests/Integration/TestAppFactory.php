<?php

declare(strict_types=1);

namespace Orders\Tests\Integration;

use DI\ContainerBuilder;
use Orders\Adapters\Inbound\OrderJsonAdapter;
use Orders\Adapters\Inbound\OrderJsonAdapterInterface;
use Orders\Adapters\Inbound\OrderDbAdapter as InboundOrderDbAdapter;
use Orders\Adapters\Inbound\OrderDbAdapterInterface as InboundOrderDbAdapterInterface;
use Orders\Adapters\Outbound\OrderDbAdapter as OutboundOrderDbAdapter;
use Orders\Adapters\Outbound\OrderDbAdapterInterface as OutboundOrderDbAdapterInterface;
use Orders\Adapters\Outbound\ProductsAdapter;
use Orders\Adapters\Outbound\ProductsAdapterInterface;
use Orders\Infra\EventPublisher;
use Orders\Infra\EventHandlers\OrderCreatedHandler;
use Orders\Infra\EventHandlers\OrderUpdatedHandler;
use Orders\Infra\Http\ExceptionHandlingMiddleware;
use Orders\Infra\Http\Handlers\CloseOrderHandler;
use Orders\Infra\Http\Handlers\GetOrderHandler;
use Orders\Infra\Http\Handlers\ListOrdersHandler;
use Orders\Infra\Http\Handlers\UpdateStatusHandler;
use Orders\Ports\Inbound\CloseOrderUseCase;
use Orders\Ports\Inbound\GetOrderUseCase;
use Orders\Ports\Inbound\ListOrdersUseCase;
use Orders\Ports\Inbound\UpdateOrderStatusUseCase;
use Orders\Ports\Outbound\EventPublisherInterface;
use Orders\Ports\Outbound\OrderRepositoryInterface;
use Orders\Ports\Outbound\ProductsServiceInterface;
use Orders\Tests\TestHelpers;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

final class TestAppFactory
{
    public static function create(): \Slim\App
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(self::getTestDefinitions());
        $container = $containerBuilder->build();

        AppFactory::setContainer($container);
        $app = AppFactory::create();

        $app->addBodyParsingMiddleware();
        $app->add(ExceptionHandlingMiddleware::class);

        $app->post('/orders/close', CloseOrderHandler::class);
        $app->get('/orders', ListOrdersHandler::class);
        $app->get('/orders/{id}', GetOrderHandler::class);
        $app->put('/orders/{id}/status', UpdateStatusHandler::class);

        return $app;
    }

    private static function getTestDefinitions(): array
    {
        $repo = TestHelpers::createInMemoryRepository();
        $productsService = TestHelpers::createMockProductsService(true);

        return [
            ResponseFactoryInterface::class => fn() => new ResponseFactory(),
            ExceptionHandlingMiddleware::class => fn(ContainerInterface $c) =>
                new ExceptionHandlingMiddleware($c->get(ResponseFactoryInterface::class)),
            OrderJsonAdapterInterface::class => fn() => new OrderJsonAdapter(),
            InboundOrderDbAdapterInterface::class => fn() => new InboundOrderDbAdapter(),
            OutboundOrderDbAdapterInterface::class => fn() => new OutboundOrderDbAdapter(),
            ProductsAdapterInterface::class => fn() => new ProductsAdapter(),
            OrderRepositoryInterface::class => fn() => $repo,
            ProductsServiceInterface::class => fn() => $productsService,
            LoggerInterface::class => function () {
                $logger = new \Monolog\Logger('test');
                $logger->pushHandler(new \Monolog\Handler\NullHandler());
                return $logger;
            },
            OrderCreatedHandler::class => fn(ContainerInterface $c) =>
                new OrderCreatedHandler($c->get(ProductsServiceInterface::class)),
            OrderUpdatedHandler::class => fn() => new OrderUpdatedHandler(),
            EventPublisherInterface::class => fn(ContainerInterface $c) => new EventPublisher(
                $c->get(LoggerInterface::class),
                $c->get(OrderCreatedHandler::class),
                $c->get(OrderUpdatedHandler::class)
            ),
            CloseOrderUseCase::class => fn(ContainerInterface $c) => new CloseOrderUseCase(
                $c->get(OrderRepositoryInterface::class),
                $c->get(EventPublisherInterface::class)
            ),
            GetOrderUseCase::class => fn(ContainerInterface $c) => new GetOrderUseCase(
                $c->get(OrderRepositoryInterface::class)
            ),
            ListOrdersUseCase::class => fn(ContainerInterface $c) => new ListOrdersUseCase(
                $c->get(OrderRepositoryInterface::class)
            ),
            UpdateOrderStatusUseCase::class => fn(ContainerInterface $c) => new UpdateOrderStatusUseCase(
                $c->get(OrderRepositoryInterface::class),
                $c->get(EventPublisherInterface::class)
            ),
            CloseOrderHandler::class => fn(ContainerInterface $c) => new CloseOrderHandler(
                $c->get(CloseOrderUseCase::class),
                $c->get(OrderJsonAdapterInterface::class)
            ),
            GetOrderHandler::class => fn(ContainerInterface $c) => new GetOrderHandler(
                $c->get(GetOrderUseCase::class)
            ),
            ListOrdersHandler::class => fn(ContainerInterface $c) => new ListOrdersHandler(
                $c->get(ListOrdersUseCase::class)
            ),
            UpdateStatusHandler::class => fn(ContainerInterface $c) => new UpdateStatusHandler(
                $c->get(UpdateOrderStatusUseCase::class)
            ),
        ];
    }
}

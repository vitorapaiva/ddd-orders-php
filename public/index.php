<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

$dependencies = require __DIR__ . '/../config/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->add(\Orders\Infra\Http\ExceptionHandlingMiddleware::class);
$app->addErrorMiddleware(true, true, true);

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();

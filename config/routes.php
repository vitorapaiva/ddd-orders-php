<?php

declare(strict_types=1);

use Orders\Infra\Http\Handlers\UpdateStatusHandler;
use Orders\Infra\Http\Handlers\GetOrderHandler;
use Orders\Infra\Http\Handlers\CloseOrderHandler;
use Orders\Infra\Http\Handlers\ListOrdersHandler;
use Slim\App;

return function (App $app) {
    $app->post('/orders/close', CloseOrderHandler::class);
    $app->get('/orders', ListOrdersHandler::class);
    $app->get('/orders/{id}', GetOrderHandler::class);
    $app->put('/orders/{id}/status', UpdateStatusHandler::class);
};

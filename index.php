<?php

use Api\Router;
use Api\Endpoint\Stats;

require_once __DIR__ . "/bootstrap.php";

Router::get("/order/averageValue", fn() => Stats::averageOrderValue());
Router::get("/products/mostPopular", fn() => Stats::mostPopularProducts());

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

Router::dispatch($method, $path);

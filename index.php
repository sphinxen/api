<?php

use Api\Router;
use Api\Endpoint\Order;
use Api\Endpoint\Product;

require_once __DIR__ . "/bootstrap.php";

Router::get("/order/averageValue", fn() => Order::averageValue());
Router::get("/products/mostPopular", fn() => Product::mostPopular());

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

Router::dispatch($method, $path);

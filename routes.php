<?php
use Api\Router;
use Api\Endpoint\Order;
use Api\Endpoint\Product;

Router::get("/order/averageValue", fn() => Order::averageValue());
Router::get("/products/mostPopular", fn() => Product::mostPopular());

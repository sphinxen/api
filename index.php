<?php

use Api\Router;
use Api\Endpoint\Stats;

require_once __DIR__ . "/bootstrap.php";

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

Router::dispatch($method, $path);

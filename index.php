<?php

use Api\Router;

require_once __DIR__ . "/bootstrap.php";
require_once __DIR__ . "/routes.php";

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

Router::dispatch($method, $path);

<?php

namespace Api;

class Router
{
    private static $routes = [];

    private static $instance = null;

    private static function getInstance()
    {
        if(is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    } 

    private function request(string $method, string $path, $handler)
    {
        self::$routes[] = [
            "method" => $method,
            "path" => $path,
            "handler" => $handler
        ];
    }

    public static function get(string $path, $handler)
    {
        self::getInstance()->request("GET",  $path,  $handler);
    }

    public static function post(string $path, $handler)
    {
        self::getInstance()->request("POST", $path, $handler);
    }

    public static function dispatch(string $method, string $path)
    {
        $router = self::getInstance();
        foreach ($router::$routes as $route) {
            if ($route["method"] === $method && $router->matchPath($route["path"], $path)) {
                $handler = $route["handler"];
                return $handler();
            }
        }

        http_response_code(404); // Not Found
        return json_encode(['error' => 'Endpoint not found']);
    }

    private static function matchPath(string $pattern, string $path): string
    {
        $pattern = preg_replace('/\//', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';
        return preg_match($pattern, $path);
    }
}
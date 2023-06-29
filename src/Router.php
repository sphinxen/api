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
        header('Content-Type: application/json; charset=utf-8');
        foreach ($router::$routes as $route) {
            if ($route["method"] === $method && $router->matchPath($route["path"], $path)) {
                $handler = $route["handler"];
                try {
                    echo json_encode($handler());
                } catch (\Exception $e) {
                    switch ($e->getCode()) {
                        case 404:
                            http_response_code(404);
                            $message = "No records found";
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    echo json_encode([
                        "error" => $message,
                    ]);
                }
                return;
            }
        }

        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Endpoint not found']);
    }

    private static function matchPath(string $pattern, string $path): string
    {
        $pattern = preg_replace('/\//', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';
        return preg_match($pattern, $path);
    }
}
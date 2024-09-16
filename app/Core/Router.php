<?php

namespace App\Core;

use App\Controllers\NotFoundController;

class Router
{
    public array $routes = [];

    public function add(string $method, string $uri, array $controller)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];

        return $this;
    }

    public function get(string $uri, array $controller)
    {
        return $this->add('GET', $uri, $controller);
    }
    public function post(string $uri, array $controller): Router
    {
        return $this->add('POST', $uri, $controller);
    }

    public function delete(string $uri, array $controller): Router
    {
        return $this->add('DELETE', $uri, $controller);
    }
    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'])['path'];

        $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {

                $controller = $route['controller'][0];
                $action = $route['controller'][1];

                if (class_exists($controller) && method_exists($controller, $action)) {
                    (new $controller)->$action();
                    return;
                }
            }
        }
        $this->abort();
    }

    protected function abort($code = 404)
    {
        http_response_code($code);

        (new NotFoundController())();

        die();
    }
}

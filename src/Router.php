<?php
/**
 * CYN Tourism - Simple Router
 * Provides clean URL routing for the MVC structure
 * Compatible with Namecheap Shared Hosting (cPanel)
 * 
 * @package CYN_Tourism
 * @version 3.1.0
 */

namespace CYN;

class Router
{
    /** @var array<string, array{controller: string, action: string}> */
    private array $routes = [];

    /**
     * Register a GET route
     */
    public function get(string $path, string $controller, string $action = 'index'): self
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'action' => $action];
        return $this;
    }

    /**
     * Register a POST route
     */
    public function post(string $path, string $controller, string $action = 'index'): self
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'action' => $action];
        return $this;
    }

    /**
     * Dispatch the current request to the appropriate controller
     */
    public function dispatch(string $uri, string $method = 'GET'): void
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');
        $method = strtoupper($method);

        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            $controllerClass = $route['controller'];
            $action = $route['action'];

            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo 'Controller not found: ' . htmlspecialchars($controllerClass);
                return;
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $action)) {
                http_response_code(500);
                echo 'Action not found: ' . htmlspecialchars($action);
                return;
            }

            $controller->$action();
            return;
        }

        // No matching route found - let legacy files handle it
        http_response_code(404);
        if (file_exists(__DIR__ . '/../404.php')) {
            include __DIR__ . '/../404.php';
        } else {
            echo '404 Not Found';
        }
    }
}

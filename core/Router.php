<?php

namespace Core;

use Exception;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private Request $request;
    private Response $response;

    public function __construct(Request $req, Response $res)
    {
        $this->request = $req;
        $this->response = $res;
    }

    // Register global middleware
    public function use(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                $middleware = new $middleware();
            }

            $this->middlewares[] = $middleware;
        }
    }

    // Add route for GET method with optional Middlewares
    public function get(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    // Add route for POST method with optional Middlewares
    public function post(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    // Add a route to the router with optional Middlewares
    public function addRoute(string $method, string $path, $handler, array $middlewares = []): void
    {
        $this->routes[$method][$this->formatPath($path)] = [
            'handler' => $handler,
            'Middlewares' => $middlewares
        ];
    }

    // Main method to run the router

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $req = $this->request;
        $res = $this->response;
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Find a matching route
        foreach ($this->routes[$method] ?? [] as $route => $routeData) {
            $pattern = preg_replace('/:([^\/]+)/', '(?<$1>[^\/]+)', $route);
            if (preg_match("#^$pattern$#", $path, $matches)) {
                array_shift($matches); // Remove the full path match
                $req->setParams($route, $matches);

                // Combine global Middlewares with route-specific Middlewares
                $allMiddlewares = array_merge($this->middlewares, $routeData['Middlewares']);

                // Process middleware and the handler
                $this->handleMiddlewares($allMiddlewares, function () use ($routeData, $req, $res) {
                    $this->resolveHandler($routeData['handler'], $req, $res);
                });
                return;
            }
        }

        // If no route matches, send 404
        http_response_code(404);
        echo "404 Not Found";
    }

    // Middleware handling

    /**
     * @throws Exception
     */
    private function handleMiddlewares(array $middlewares, callable $handler): void
    {
        $req = $this->request;
        $res = $this->response;

        $next = function () use ($handler, $req, $res) {
            $handler($req, $res);
        };

        foreach (array_reverse($middlewares) as $middleware) {
            // If middleware is a class name, instantiate the class
            if (is_string($middleware)) {
                $middleware = new $middleware();
            }

            // Ensure middleware is callable (e.g., it must have a handle method)
            if (is_callable($middleware)) {
                $next = fn() => $middleware($req, $res, $next);
            } elseif (method_exists($middleware, 'handle')) {
                $next = fn() => $middleware->handle($req, $res, $next);
            } else {
                throw new Exception('Invalid middleware provided');
            }
        }

        $next(); // Execute the chain
    }

    /**
     * @throws Exception
     */
    private function resolveHandler($handler, Request $req, Response $res): void
    {
        if (is_callable($handler)) {
            // Directly call the handler if it's callable (e.g., a closure)
            $handler($req, $res);
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controller, $methodName] = $handler;

            // Check if the first element is a class name (string) or a controller instance (object)
            if (is_string($controller)) {
                // If it's a string, check if the class exists
                if (!class_exists($controller)) {
                    throw new Exception("Controller class $controller does not exist");
                }

                // Instantiate the controller
                $controller = new $controller();
            }

            // Ensure the method exists in the controller
            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method $methodName does not exist in controller " . get_class($controller));
            }

            // Call the method on the controller instance
            $controller->$methodName($req, $res);
            return;
        }

        // Invalid handler, throw an exception
        throw new Exception('Invalid route handler');
    }

    private function formatPath(string $path): string
    {
        return rtrim($path, '/') ?: '/';
    }
}
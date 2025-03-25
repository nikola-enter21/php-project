<?php

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

    public function use(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    private function handleMiddlewares(callable $handler): void
    {
        $req = $this->request;
        $res = $this->response;

        $next = function () use ($handler, $req, $res) {
            $handler($req, $res);
        };

        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = fn() => $middleware($req, $res, $next);
        }

        $next();
    }

    public function addRoute($method, $path, $handler): void
    {
        $this->routes[$method][$this->formatPath($path)] = $handler;
    }

    public function get($path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function run(): void
    {
        $req = $this->request;
        $res = $this->response;
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/:\w+/', '([^/]+)', $route);
            if (preg_match("#^$pattern$#", $path, $matches)) {
                array_shift($matches);
                $req->setParams($route, $matches);

                // Apply middleware before executing handler
                $this->handleMiddlewares(fn() => $handler($req, $res));
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function formatPath($path): string
    {
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}

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

    // Register middleware
    public function use(callable|array $middleware): void
    {
        // If middleware is passed as [ClassName::class, 'handle'], instantiate the class
        if (is_array($middleware) && is_string($middleware[0])) {
            $middleware[0] = new $middleware[0]();
        }

        // Add the middleware to the queue
        $this->middlewares[] = $middleware;
    }


    // Add route for GET method
    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    // Add route for POST method
    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    // Add a route to the router
    public function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[$method][$this->formatPath($path)] = $handler;
    }

    // Main method to run the router
    public function run(): void
    {
        $req = $this->request;
        $res = $this->response;
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Find a matching route
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/:\w+/', '([^/]+)', $route);
            if (preg_match("#^$pattern$#", $path, $matches)) {
                array_shift($matches); // Remove the full path match
                $req->setParams($route, $matches);

                // Process middleware and the handler
                $this->handleMiddlewares(function () use ($handler, $req, $res) {
                    $this->resolveHandler($handler, $req, $res);
                });
                return;
            }
        }

        // If no route matches, send 404
        http_response_code(404);
        echo "404 Not Found";
    }

    // Middleware handling
    private function handleMiddlewares(callable $handler): void
    {
        $req = $this->request;
        $res = $this->response;

        // Start with the last middleware and wrap them
        $next = function () use ($handler, $req, $res) {
            $handler($req, $res);
        };

        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = fn() => $middleware($req, $res, $next);
        }

        $next(); // Execute the chain
    }

    // Resolve and execute the route handler (closure, function, or class method)

    /**
     * @throws Exception
     */
    private function resolveHandler($handler, Request $req, Response $res): void
    {
        // If the handler is a callable function or closure
        if (is_callable($handler)) {
            $handler($req, $res);
            return;
        }

        // If the handler is an array [ControllerClass, 'method']
        if (is_array($handler) && count($handler) === 2) {
            [$className, $methodName] = $handler;

            // Check if the class and method exist
            if (!class_exists($className)) {
                throw new Exception("Controller class $className does not exist");
            }

            $controller = new $className();

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method $methodName does not exist in controller $className");
            }

            // Call the controller method
            $controller->$methodName($req, $res);
            return;
        }

        // If the handler is invalid, throw an error
        throw new Exception('Invalid route handler');
    }

    // Format the path to avoid trailing slashes for consistency
    private function formatPath(string $path): string
    {
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
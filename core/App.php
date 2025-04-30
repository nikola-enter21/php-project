<?php

namespace Core;

use Exception;

class App
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function run(): void
    {
        try {
            $this->router->run();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
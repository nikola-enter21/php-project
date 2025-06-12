<?php

namespace Core;

use JetBrains\PhpStorm\NoReturn;

class Response
{
    public function send(string $text): void
    {
        echo $text;
    }

    public function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function status(int $code): static
    {
        http_response_code($code);
        return $this;
    }

    public function view(string $file, array $data = []): void
    {
        extract($data);
        $viewPath = "./app/Views/$file.php";

        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("View file not found: $viewPath");
        }

        include $viewPath;
    }

    /**
     * Redirect to a given URL.
     *
     * @param string $url The target URL for redirection.
     * @param int $statusCode Optional HTTP status code for the redirection (default: 302).
     */
    #[NoReturn] public function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit();
    }
}

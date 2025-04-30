<?php

namespace Core;

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
        $viewPath = __DIR__ . "/../app/Views/$file.php";

        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("View file not found: $viewPath");
        }

        include $viewPath;
    }
}

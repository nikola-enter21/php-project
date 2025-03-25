<?php

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
        include "views/$file.php";
    }
}

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
        $viewPath = __DIR__ . "/../app/Views/$file.php";

        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("View file not found: $viewPath");
        }

        //Add BASE_PATH to the data array for all views
        $data['BASE_PATH'] = BASE_PATH;
        extract($data);

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
        // If the URL is already an absolute URL its all cool
        if (preg_match('/^https?:\/\//', $url)) {
            http_response_code($statusCode);
            header("Location: $url");
            exit();
        }

        // If the URL starts with a slash, its a root-relative URL
        if (str_starts_with($url, '/')) {
            // Remove any existing base path 
            $url = preg_replace('/^' . preg_quote(BASE_PATH, '/') . '/', '', $url);
            // Add the base path back
            $url = BASE_PATH . $url;
        } else {
            $url = BASE_PATH . '/' . $url;
        }

        // Remove any double slashes except for the protocol just in case
        $url = preg_replace('#([^:])//+#', '$1/', $url);
        
        http_response_code($statusCode);
        header("Location: $url");
        exit();
    }
}

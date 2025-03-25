<?php

require 'Session.php';
require 'Database.php';

class Request
{
    private array $params = [];
    private Session $session;
    private Database $db;

    public function __construct(Database $db)
    {
        $this->session = new Session();
        $this->db = $db;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function db(): Database
    {
        return $this->db;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function query(?string $key = null)
    {
        return $key !== null ? ($_GET[$key] ?? null) : $_GET;
    }

    public function body(?string $key = null)
    {
        $body = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        return $key !== null ? ($body[$key] ?? null) : $body;
    }

    public function param(string $key): ?string
    {
        return $this->params[$key] ?? null;
    }

    public function setParams(string $route, array $values): void
    {
        preg_match_all('/:([\w]+)/', $route, $keys);
        foreach ($keys[1] as $index => $key) {
            $this->params[$key] = $values[$index] ?? null;
        }
    }
}

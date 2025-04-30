<?php

namespace Core;

use Exception;

class Container
{
    private array $services = [];
    private array $instances = [];

    public function set(string $key, callable $definition): void
    {
        $this->services[$key] = $definition;
    }

    /**
     * @throws Exception
     */
    public function get(string $key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->services[$key])) {
            throw new Exception("Service {$key} not found in the container.");
        }

        $this->instances[$key] = ($this->services[$key])($this);
        return $this->instances[$key];
    }
}
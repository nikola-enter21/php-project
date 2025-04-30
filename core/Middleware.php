<?php

namespace Core;

abstract class Middleware
{
    abstract public function handle(Request $req, Response $res, callable $next): void;
}
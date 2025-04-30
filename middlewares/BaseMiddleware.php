<?php

abstract class Middleware
{
    /**
     * Handle the middleware logic.
     *
     * @param Request $req
     * @param Response $res
     * @param callable $next
     * @return void
     */
    abstract public function handle(Request $req, Response $res, callable $next): void;
}
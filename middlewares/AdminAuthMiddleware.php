<?php

require_once 'BaseMiddleware.php';

class AdminAuthMiddleware extends Middleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        // Check if the user is authorized for admin routes
        if (str_starts_with($req->path(), '/admin') && !$req->session()->has('user')) {
            $res->status(401)->json(['error' => 'Unauthorized']);
            return;
        }

        // Proceed to the next middleware or route handler
        $next();
    }
}
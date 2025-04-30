<?php

namespace App\Middlewares;

use Core\Middleware;
use Core\Request;
use Core\Response;

class AuthMiddleware extends Middleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        // Check if the user is authenticated
        if (!$req->session()->has('user')) {
            $res->status(401)->json(['error' => 'Unauthorized']);
            return;
        }

        // Proceed to the next middleware or route handler
        $next();
    }
}
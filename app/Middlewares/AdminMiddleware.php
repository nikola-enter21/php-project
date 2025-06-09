<?php

namespace App\Middlewares;

use Core\Middleware;
use Core\Request;
use Core\Response;

class AdminMiddleware extends Middleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        // Check if the user is authenticated and is an admin
        if (!$req->session()->has('user') || $req->session()->get('user')['role'] !== 'admin') {
            $res->status(403)->json(['error' => 'Forbidden']);
            return;
        }

        // Proceed to the next middleware or route handler
        $next();
    }
}
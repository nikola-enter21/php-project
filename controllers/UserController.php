<?php

class UserController
{
    // Handles the home page route (GET /)
    public function index(Request $req, Response $res): void
    {
        $res->send('<h1>Welcome, John!</h1>');
    }

    // Handles GET /user/:id
    public function getUserById(Request $req, Response $res): void
    {
        $id = $req->param('id');

        // Simulate fetching user details
        $res->json(['user_id' => $id, 'name' => 'John Doe']);
    }
}
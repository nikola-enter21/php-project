<?php

class AdminController
{
    // Handles GET /admin
    public function dashboard(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');

        if ($user) {
            $res->json(['message' => 'Welcome, Admin!', 'user' => $user]);
        } else {
            $res->status(401)->json(['error' => 'Unauthorized']);
        }
    }
}
<?php

class AdminController
{
    // Handles GET /admin
    public function dashboard(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');
        $res->json(['message' => 'Welcome, Admin!', 'user' => $user]);
    }
}
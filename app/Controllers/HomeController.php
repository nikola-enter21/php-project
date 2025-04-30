<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\UserModel;

class HomeController
{
    /**
     * Mock function: Return a list of users.
     */
    public function index(Request $req, Response $res): void
    {
        $res->view('home', ['name' => 'John Doe']);
    }
}

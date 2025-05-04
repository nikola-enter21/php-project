<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\UserModel;

class UserController
{
    private UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function loginView(Request $req, Response $res): void
    {
        $res->view('login');
    }

    public function registerView(Request $req, Response $res): void
    {
        $res->view('register');
    }
}

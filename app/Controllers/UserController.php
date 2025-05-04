<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\UserModel;
use JetBrains\PhpStorm\NoReturn;

class UserController
{
    private UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function loginView(Request $req, Response $res): void
    {
        if ($req->session()->has('user')) {
            $res->redirect('/');
        }

        $res->view('login');
    }

    public function registerView(Request $req, Response $res): void
    {
        if ($req->session()->has('user')) {
            $res->redirect('/');
        }

        $res->view('register');
    }

    public function login(Request $req, Response $res): void
    {
        $email = $req->body('email');
        $password = $req->body('password');

        // Fetch user from DB by email
        $user = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'test@abv.bg',
            'password' => password_hash('12345', PASSWORD_BCRYPT)
        ];

        if (!$user || !password_verify($password, $user['password'])) {
            $res->json(['success' => false, 'message' => 'Invalid email or password'], 401);
            return;
        }

        // Login successful
        $req->session()->set('user', $user);
        $res->json(['success' => true, 'message' => 'Login successful', 'user' => $user['name']]);
    }

    // Handle user registration
    public function register(Request $req, Response $res): void
    {
        $name = $req->body('name');
        $email = $req->body('email');
        $password = $req->body('password');
        $confirmPassword = $req->body('confirm_password');

        // Validate input
        if ($password !== $confirmPassword) {
            $res->json(['success' => false, 'message' => 'Passwords do not match'], 400);
            return;
        }

        // Check if the email is already in use
        if ($this->isEmailTaken($email)) {
            $res->json(['success' => false, 'message' => 'Email already registered'], 400);
            return;
        }

        $newUser = [
            'id' => random_int(1, 10000000),
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ];
        $req->session()->set('user', $newUser);
        $res->json(['success' => true, 'message' => 'Registration successful']);
    }

    #[NoReturn] public function logout(Request $req, Response $res): void
    {
        $req->session()->destroy();
        $res->redirect('/login');
    }

    // Helper to get a user by email
    private function getUserByEmail(string $email): ?array
    {
        return null;
    }

    // Helper to check if an email is already taken
    private function isEmailTaken(string $email): bool
    {
        return $this->getUserByEmail($email) !== null;
    }

}

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

    /**
     * Show the login view.
     */
    public function loginView(Request $req, Response $res): void
    {
        if ($req->session()->has('user')) {
            $res->redirect('/');
        }

        $res->view('login');
    }

    /**
     * Show the register view.
     */
    public function registerView(Request $req, Response $res): void
    {
        if ($req->session()->has('user')) {
            $res->redirect('/');
        }

        $res->view('register');
    }

    /**
     * Handle user login logic.
     */
    public function login(Request $req, Response $res): void
    {
        $email = trim($req->body('email'));
        $password = $req->body('password');

        // Basic validation
        if (empty($email) || empty($password)) {
            $res->json(['success' => false, 'message' => 'Email and password are required'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $res->json(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        // Fetch user from the database
        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $res->json(['success' => false, 'message' => 'Invalid email or password'], 401);
            return;
        }

        // Successful login
        unset($user['password']); // Remove password before storing in session
        $req->session()->set('user', $user);
        $res->json(['success' => true, 'message' => 'Login successful!', 'user' => $user]);
    }

    /**
     * Handle user registration logic.
     */
    public function register(Request $req, Response $res): void
    {
        $fullName = trim($req->body('name'));
        $email = trim($req->body('email'));
        $password = $req->body('password');
        $confirmPassword = $req->body('confirm_password');

        // Validation
        if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
            $res->json(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $res->json(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        if ($password !== $confirmPassword) {
            $res->json(['success' => false, 'message' => 'Passwords do not match'], 400);
            return;
        }

        if (strlen($password) < 6) {
            $res->json(['success' => false, 'message' => 'Password must be at least 6 characters long'], 400);
            return;
        }

        // Check if email is already in use
        if ($this->userModel->isEmailTaken($email)) {
            $res->json(['success' => false, 'message' => 'Email is already registered'], 400);
            return;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare user data
        $userData = [
            'full_name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword,
        ];

        // Insert user into the database
        if (!$this->userModel->createUser($userData)) {
            $res->json(['success' => false, 'message' => 'Failed to register the user'], 500);
            return;
        }

        // Successful registration
        $res->json(['success' => true, 'message' => 'Registration successful!']);
    }

    /**
     * Log out the user.
     */
    #[NoReturn] public function logout(Request $req, Response $res): void
    {
        $req->session()->destroy();
        $res->redirect('/login');
    }
}
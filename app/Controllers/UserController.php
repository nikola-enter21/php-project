<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\UserModel;
use App\Models\LogModel;
use JetBrains\PhpStorm\NoReturn;

class UserController
{
    private UserModel $userModel;
    private LogModel $logModel;

    public function __construct(UserModel $userModel, LogModel $logModel)
    {
        $this->userModel = $userModel;
        $this->logModel = $logModel;
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
            $this->logModel->createLog(null, 'login', "Failed login attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Email and password are required'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logModel->createLog(null, 'login', "Failed login attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        // Fetch user from the database
        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->logModel->createLog(null, 'login', "Failed login attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Invalid email or password'], 401);
            return;
        }

        // Successful login
        unset($user['password']); // Remove password before storing in session
        $req->session()->set('user', $user);
        if ($user) {
            $this->logModel->createLog($user['id'], 'login', "Successful login with email: $email");
        }
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
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email");
            $res->json(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        if ($password !== $confirmPassword) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Passwords do not match'], 400);
            return;
        }

        if (strlen($password) < 6) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Password must be at least 6 characters long'], 400);
            return;
        }

        // Check if email is already in use
        if ($this->userModel->isEmailTaken($email)) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email");
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
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Failed to register the user'], 500);
            return;
        }

        // Successful registration
        $this->logModel->createLog(null, 'register', "Successful registration with email: $email");
        $res->json(['success' => true, 'message' => 'Registration successful!']);
    }

    public function deleteUser(Request $req, Response $res)
    {
        $userId = $req->param('id');
        $loggedUser = $req->session()->get('user');

        if (!$userId) {
            $this->logModel->createLog($loggedUser['id'], 'delete_user', "Failed to delete user: Invalid user ID");
            return $res->json(['success' => false, 'message' => 'Invalid user ID'], 400);
        }

        if ($loggedUser['role'] !== 'admin' && $loggedUser['id'] !== $userId) {
            $this->logModel->createLog($loggedUser['id'], 'delete_user', "Unauthorized delete attempt by user ID: {$loggedUser['id']} on user ID: $userId");
            $res->json(['success' => false, 'message' => 'You are not authorized to delete this quote.'], 403);
            return;
        }

        if ($this->userModel->delete($userId)) {
            $this->logModel->createLog($loggedUser['id'], 'delete_user', "User deleted successfully: $userId");
            return $res->json(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            $this->logModel->createLog($loggedUser['id'], 'delete_user', "Failed to delete user: $userId");
            return $res->json(['success' => false, 'message' => 'Failed to delete user'], 500);
        }
    }

    /**
     * Log out the user.
     */
    #[NoReturn] public function logout(Request $req, Response $res): void
    {
        $this->logModel->createLog($req->session()->get('user')['id'], 'logout', 'User logged out successfully');
        $req->session()->destroy();
        $res->redirect('/login');
    }
}
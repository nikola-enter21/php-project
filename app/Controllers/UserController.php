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

    public function loginView(Request $req, Response $res): void
    {
        if ($req->session()->has('user')) {
            $res->redirect('/');
            return;
        }
        $res->view('login');
    }

    public function registerView(Request $req, Response $res): void
    {
        if ($req->session()->has('user')) {
            $res->redirect('/');
            return;
        }
        $res->view('register');
    }

    public function login(Request $req, Response $res): void
    {
        $email = trim($req->body('email'));
        $password = $req->body('password');

        if (empty($email) || empty($password)) {
            $this->logModel->createLog(null, 'login', "Failed login attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Email and password are required'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logModel->createLog(null, 'login', "Failed login attempt with invalid email format: $email");
            $res->json(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->logModel->createLog(null, 'login', "Failed login attempt with email: $email");
            $res->json(['success' => false, 'message' => 'Invalid email or password'], 401);
            return;
        }

        unset($user['password']); // Remove password before session storage
        $req->session()->set('user', $user);

        $this->logModel->createLog($user['id'], 'login', "Successful login with email: $email");
        $res->json(['success' => true, 'message' => 'Login successful!', 'user' => $user]);
    }

    public function register(Request $req, Response $res): void
    {
        $fullName = trim($req->body('name'));
        $email = trim($req->body('email'));
        $password = $req->body('password');
        $confirmPassword = $req->body('confirm_password');

        if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email");
            $res->json(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with invalid email format: $email");
            $res->json(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        if ($password !== $confirmPassword) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email due to password mismatch");
            $res->json(['success' => false, 'message' => 'Passwords do not match'], 400);
            return;
        }

        if (strlen($password) < 6) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email due to weak password");
            $res->json(['success' => false, 'message' => 'Password must be at least 6 characters long'], 400);
            return;
        }

        if ($this->userModel->isEmailTaken($email)) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email because email is already registered");
            $res->json(['success' => false, 'message' => 'Email is already registered'], 400);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userData = [
            'full_name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword,
        ];

        if (!$this->userModel->createUser($userData)) {
            $this->logModel->createLog(null, 'register', "Failed registration attempt with email: $email due to DB error");
            $res->json(['success' => false, 'message' => 'Failed to register the user'], 500);
            return;
        }

        $this->logModel->createLog(null, 'register', "Successful registration with email: $email");
        $res->json(['success' => true, 'message' => 'Registration successful!']);
    }

    public function deleteUser(Request $req, Response $res): void
    {
        $userId = $req->param('id');
        $loggedUser = $req->session()->get('user');

        if (!$userId) {
            $this->logModel->createLog($loggedUser['id'] ?? null, 'delete_user', "Failed to delete user: Invalid user ID");
            $res->json(['success' => false, 'message' => 'Invalid user ID'], 400);
            return;
        }

        if (($loggedUser['role'] ?? '') !== 'admin' && ($loggedUser['id'] ?? '') !== $userId) {
            $this->logModel->createLog($loggedUser['id'] ?? null, 'delete_user', "Unauthorized delete attempt by user ID: {$loggedUser['id']} on user ID: $userId");
            $res->json(['success' => false, 'message' => 'You are not authorized to delete this user.'], 403);
            return;
        }

        if ($this->userModel->delete($userId)) {
            $this->logModel->createLog($loggedUser['id'], 'delete_user', "User deleted successfully: $userId");
            $res->json(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            $this->logModel->createLog($loggedUser['id'], 'delete_user', "Failed to delete user: $userId");
            $res->json(['success' => false, 'message' => 'Failed to delete user'], 500);
        }
    }

    #[NoReturn]
    public function logout(Request $req, Response $res): void
    {
        $userId = $req->session()->get('user')['id'] ?? null;
        if ($userId) {
            $this->logModel->createLog($userId, 'logout', 'User logged out successfully');
        }
        $req->session()->destroy();
        $res->redirect('/login');
    }

    public function showUserProfile(Request $req, Response $res): void
    {
        $loggedUser = $req->session()->get('user');
        $userId = $req->param('id');

        if (!$loggedUser || $loggedUser['id'] !== $userId) {
            $res->redirect('/login');
            return;
        }

        $user = $this->userModel->findById($userId);

        if (!$user) {
            $res->json(['success' => false, 'message' => 'User not found'], 404);
            return;
        }

        $res->view('profile', ['user' => $user]);
    }

    public function changeUserPassword(Request $req, Response $res): void
    {
        $loggedUser = $req->session()->get('user');
        $userId = $req->param('id');

        if (!$loggedUser || $loggedUser['id'] !== $userId) {
            $res->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }

        $oldPassword = $req->body('oldPassword');
        $newPassword = $req->body('newPassword');
        $confirmPassword = $req->body('confirmPassword');

        $user = $this->userModel->findById($userId);

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $res->json(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (!password_verify($oldPassword, $user['password'])) {
            $res->json(['success' => false, 'message' => 'Old password is incorrect'], 401);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $res->json(['success' => false, 'message' => 'New passwords do not match'], 400);
            return;
        }

        if (strlen($newPassword) < 6) {
            $res->json(['success' => false, 'message' => 'New password must be at least 6 characters long'], 400);
            return;
        }

        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        if ($this->userModel->updatePassword($userId, $hashedNewPassword)) {
            $loggedUser['password'] = $hashedNewPassword;
            $req->session()->set('user', $loggedUser);

            $this->logModel->createLog($userId, 'change_password', "User with id $userId changed their password successfully");
            $res->json(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            $this->logModel->createLog($userId, 'change_password', "User with id $userId to change user password");
            $res->json(['success' => false, 'message' => 'Failed to change password'], 500);
        }
    }
}

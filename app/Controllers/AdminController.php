<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\UserModel;

class AdminController
{
    private UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel; // Accept the UserModel but don’t use it in mock functions
    }

    /**
     * Mock function: Display the admin dashboard.
     */
    public function dashboard(Request $req, Response $res)
    {
        // Mock response for admin dashboard
        $mockData = [
            'totalUsers' => 100,
            'activeUsers' => 80,
            'pendingTasks' => 5
        ];

        $res->json($mockData);
    }

    /**
     * Mock function: Manage user roles.
     */
    public function manageRoles(Request $req, Response $res)
    {
        // Mock managing user roles (based on request body)
        $mockData = $req->body(); // Assume role data is posted
        $res->json(['success' => true, 'message' => 'Roles updated', 'data' => $mockData]);
    }

    /**
     * Mock function: View logs.
     */
    public function viewLogs(Request $req, Response $res)
    {
        // Mock response for logs
        $mockLogs = [
            ['id' => 1, 'action' => 'User Login', 'timestamp' => '2023-10-01 10:00:00'],
            ['id' => 2, 'action' => 'User Logout', 'timestamp' => '2023-10-01 12:00:00'],
            ['id' => 3, 'action' => 'User Created', 'timestamp' => '2023-10-01 14:00:00'],
        ];

        $res->json($mockLogs);
    }

    /**
     * Mock function: Delete system logs.
     */
    public function deleteLogs(Request $req, Response $res)
    {
        // Mock deleting logs
        $res->json(['success' => true, 'message' => 'Logs cleared']);
    }
}
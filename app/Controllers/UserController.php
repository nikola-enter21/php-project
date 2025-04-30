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
        $this->userModel = $userModel; // Accept the UserModel but don’t use it in mock functions
    }

    /**
     * Mock function: Return a list of users.
     */
    public function index(Request $req, Response $res): void
    {
        // Mock response (instead of querying the database)
        $mockUsers = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com']
        ];

        $res->json($mockUsers);
    }

    /**
     * Mock function: Return the details of a single user.
     */
    public function getUserById(Request $req, Response $res): void
    {
        // Extract the ID from request parameters
        $id = (int)$req->param('id');

        // Mock response based on ID
        $mockUser = ($id === 1)
            ? ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com']
            : null;

        if ($mockUser) {
            $res->json($mockUser);
        } else {
            $res->status(404)->json(['error' => 'User not found']);
        }
    }

    /**
     * Mock function: Create a new user.
     */
    public function createUser(Request $req, Response $res): void
    {
        // Mock creating a user and returning success
        $mockData = $req->body(); // Assume the user data is passed in request body

        // Return a success message with mock ID
        $res->json(['success' => true, 'id' => 3, 'data' => $mockData]);
    }

    /**
     * Mock function: Delete a user.
     */
    public function deleteUser(Request $req, Response $res): void
    {
        // Extract the ID from request parameters
        $id = (int)$req->param('id');

        // Mock deleting a user
        if ($id > 0) {
            $res->json(['success' => true, 'message' => "User with ID {$id} deleted"]);
        } else {
            $res->status(400)->json(['error' => 'Invalid user ID']);
        }
    }
}

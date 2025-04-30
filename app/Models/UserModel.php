<?php

namespace App\Models;

use Core\BaseModel;
use Core\Database;

class UserModel extends BaseModel
{
    protected string $table = 'users'; // The table name

    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    /**
     * Get all users.
     */
    public function getAllUsers(): array
    {
        return $this->findAll();
    }

    /**
     * Get a user by ID.
     */
    public function getUserById(int $id): ?array
    {
        return $this->findById($id);
    }

    /**
     * Create a new user record.
     */
    public function createUser(array $data): bool
    {
        return $this->create($data);
    }

    /**
     * Update a user record.
     */
    public function updateUser(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Delete a user record.
     */
    public function deleteUser(int $id): bool
    {
        return $this->delete($id);
    }
}
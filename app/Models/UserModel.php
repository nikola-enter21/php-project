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
     * Insert a new user into the database.
     *
     * @param array $data Contains full_name, email, password (hashed).
     * @return bool True on success or false on failure.
     */
    public function createUser(array $data): bool
    {
        return $this->create($data);
    }

    /**
     * Get a user by their email address.
     *
     * @param string $email The email to search for.
     * @return array|null The user's data (associative array) or null if not found.
     */
    public function getUserByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        return $this->db->querySingle($sql, ['email' => $email]);
    }

    /**
     * Check if an email is already registered in the system.
     *
     * @param string $email The email address to check.
     * @return bool True if the email exists, false otherwise.
     */
    public function isEmailTaken(string $email): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email";
        return $this->db->querySingle($sql, ['email' => $email]) !== null;
    }

    public function updateUserRole(string $userId, string $role): bool 
    {
        return $this->update($userId, ['role' => $role]);
    }

    public function searchUsersExcluding(string $search, string $excludeUserId): array 
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (full_name LIKE :search OR email LIKE :search) 
                AND id != :excludeUserId 
                ORDER BY full_name ASC";
        
        return $this->db->query($sql, [
            'search' => "%{$search}%",
            'excludeUserId' => $excludeUserId
        ]);
    }
}
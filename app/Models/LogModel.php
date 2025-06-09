<?php

namespace App\Models;

use Core\BaseModel;

class LogModel extends BaseModel
{
    protected string $table = 'Logs';

    public function createLog(string $userId, string $action, string $details): bool
    {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
        ]);
    }

    public function getFilteredLogs(string $search): array
    {
        $sql = "SELECT l.*, u.full_name AS user_name 
        FROM {$this->table} l
        LEFT JOIN Users u ON l.user_id = u.id
        WHERE l.action LIKE :search OR l.details LIKE :search OR u.full_name LIKE :search
        ORDER BY l.created_at DESC";
        return $this->db->fetchAll($sql, ['search' => "%{$search}%"]);
    }

    public function deleteAllLogs(): bool 
    {
        $sql = "DELETE FROM {$this->table}";
        return $this->db->execute($sql);
    }
}
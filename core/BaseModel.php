<?php

namespace Core;

abstract class BaseModel
{
    protected Database $db;
    protected string $table;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all records from the table.
     */
    public function findAll(): array
    {
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }

    /**
     * Find a record by ID.
     */
    public function findById(string $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $result = $this->db->query($sql, ['id' => $id]);
        return $result[0] ?? null;
    }

    /**
     * Create a new record in the table.
     */
    public function create(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        return $this->db->execute($sql, $data);
    }

    /**
     * Update a record by ID.
     */
    public function update(string $id, array $data): bool
    {
        $setClause = implode(', ', array_map(fn($key) => "{$key} = :{$key}", array_keys($data)));
        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        return $this->db->execute($sql, $data);
    }

    /**
     * Delete a record by ID.
     */
    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    public function getTotalCount(): int 
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->querySingle($sql);
        return (int) $result['count'];
    }
}
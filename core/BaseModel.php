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
        return $this->db->query("SELECT * FROM {$this->table}");
    }

    /**
     * Find a record by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->querySingle("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
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
    public function update(int $id, array $data): bool
    {
        $setClause = implode(', ', array_map(fn($key) => "{$key} = :{$key}", array_keys($data)));
        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        return $this->db->execute($sql, $data);
    }

    /**
     * Delete a record by ID.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }
}
<?php

namespace App\Models;

use App\Core\Database;
use Dompdf\Dompdf;
use Core\BaseModel;

class CollectionModel extends BaseModel
{
    protected string $table = 'collections';
    protected Database $db;

    public function __construct()
    {
        $this->db = new Database(); // Initialize the database connection
    }

    /**
     * Add a quote to a collection.
     */
    public function addQuoteToCollection(int $collectionId, string $quote): bool
    {
        $sql = "INSERT INTO {$this->table} (collection_id, quote) VALUES (:collection_id, :quote)";
        return $this->db->execute($sql, [
            'collection_id' => $collectionId,
            'quote' => $quote,
        ]);
    }

    /**
     * Retrieve all collections.
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

     /**
     * Retrieve a collection by its ID.
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Create a new collection.
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (name) VALUES (:name)";
        return $this->db->execute($sql, [
            'name' => $data['name'],
        ]);
    }

    /**
     * Update an existing collection by its ID.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET name = :name WHERE id = :id";
        return $this->db->execute($sql, [
            'id' => $id,
            'name' => $data['name'],
        ]);
    }

        /**
     * Delete a collection by its ID.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Delete a quote from a collection.
     */
    public function deleteQuoteFromCollection(int $collectionId, int $quoteId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE collection_id = :collection_id AND id = :quote_id";
        return $this->db->execute($sql, [
            'collection_id' => $collectionId,
            'quote_id' => $quoteId,
        ]);
    }
}
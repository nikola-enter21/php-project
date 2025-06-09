<?php

namespace App\Models;

use App\Core\Database;
use Dompdf\Dompdf;
use Core\BaseModel;

class CollectionModel extends BaseModel
{
    protected string $table = 'collections';

    /**
     * Add a quote to a collection.
     */
    public function addQuoteToCollection(string $collectionId, string $quoteId): bool
    {
        try {
            $sqlCheck = "SELECT 1 FROM collection_quotes WHERE collection_id = :collection_id AND quote_id = :quote_id";
            $exists = $this->db->fetch($sqlCheck, [
                'collection_id' => $collectionId,
                'quote_id' => $quoteId,
            ]);

            if ($exists) {
                error_log("Quote already exists in the collection.");
                return false;
            }

            $sqlInsert = "INSERT INTO collection_quotes (collection_id, quote_id) VALUES (:collection_id, :quote_id)";
            $result = $this->db->execute($sqlInsert, [
                'collection_id' => $collectionId,
                'quote_id' => $quoteId,
            ]);

            error_log("SQL Insert Result: " . ($result ? 'Success' : 'Failure'));
            return $result;
        } catch (\Exception $e) {
            error_log('Error adding quote to collection: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve all collections.
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    public function getAllCollectionsByUserId(string $userId): array
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
            $collections = $this->db->fetchAll($sql, ['user_id' => $userId]);
            error_log('SQL Result: ' . json_encode($collections)); // Логване на резултата от SQL заявката
            return $collections;
        } catch (\Exception $e) {
            error_log('Error fetching collections: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve all collections with their quotes.
     */
    public function getAllCollectionsWithQuotes(string $userId): array
    {
        $sql = "SELECT c.*, q.title AS quote_title, q.content AS quote_content, q.author AS quote_author
                FROM collections c
                LEFT JOIN collection_quotes cq ON c.id = cq.collection_id
                LEFT JOIN quotes q ON cq.quote_id = q.id
                WHERE c.user_id = :user_id";
        $rows = $this->db->fetchAll($sql, ['user_id' => $userId]);

        $collections = [];
        foreach ($rows as $row) {
            $id = $row['id'];
            if (!isset($collections[$id])) {
                $collections[$id] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'quotes' => []
                ];
            }
            if ($row['quote_title'] || $row['quote_content'] || $row['quote_author']) {
                $collections[$id]['quotes'][] = [
                    'id' => $row['id'],
                    'title' => $row['quote_title'],
                    'content' => $row['quote_content'],
                    'author' => $row['quote_author']
                ];
            }
        }

        return array_values($collections);
    }

     /**
     * Retrieve a collection by its ID.
     */
    public function findById(string $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Create a new collection.
     */
    public function createCollection(array $data): bool
    {
        return $this->create($data);
    }

    /**
     * Delete a quote from a collection.
     */
    public function deleteQuoteFromCollection(string $collectionId, string $quoteId): bool
    {
        $sql = "DELETE FROM collection_quotes WHERE collection_id = :collection_id AND quote_id = :quote_id";
        return $this->db->execute($sql, [
            'collection_id' => $collectionId,
            'quote_id' => $quoteId,
        ]);
    }

    /**
     * Retrieve quotes by collection ID.
     */
    public function getQuotesByCollectionId(string $collectionId): array
    {
        $sql = "SELECT q.title, q.content, q.author 
                FROM collection_quotes cq
                JOIN quotes q ON cq.quote_id = q.id
                WHERE cq.collection_id = :collection_id";
        return $this->db->fetchAll($sql, ['collection_id' => $collectionId]);
    }
}
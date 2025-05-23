<?php

namespace App\Models;

use App\Core\Database;
use Dompdf\Dompdf;

class CollectionModel
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
    public function addQuoteToCollection(string $collectionId, string $quote): bool
    {
        $sql = "INSERT INTO {$this->table} (collection_id, quote) VALUES (:collection_id, :quote)";
        return $this->db->execute($sql, [
            'collection_id' => $collectionId,
            'quote' => $quote,
        ]);
    }

    /**
     * Delete a quote from a collection.
     */
    public function deleteQuoteFromCollection(string $collectionId, string $quoteId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE collection_id = :collection_id AND id = :quote_id";
        return $this->db->execute($sql, [
            'collection_id' => $collectionId,
            'quote_id' => $quoteId,
        ]);
    }

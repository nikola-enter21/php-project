<?php

namespace App\Models;

use Core\BaseModel;

class QuoteModel extends BaseModel
{
    protected string $table = 'Quotes';

    /**
     * Get counts of likes, saves and reports for a quote
     */
    public function getQuoteCounts(string $quoteId): array
    {
        $sql = "SELECT 
            (SELECT COUNT(*) FROM Likes WHERE quote_id = :quote_id) as likes_count,
            (SELECT COUNT(*) FROM Booked WHERE quote_id = :quote_id) as saves_count,
            (SELECT COUNT(*) FROM Reports WHERE quote_id = :quote_id) as reports_count";

        return $this->db->query($sql, ['quote_id' => $quoteId])[0] ?? [
            'likes_count' => 0,
            'saves_count' => 0,
            'reports_count' => 0
        ];
    }

    /**
     * Fetch all quotes from the database with their interaction counts
     */
    public function getAllQuotes(?string $userId = null): array
    {
        $quotes = $this->findAll();
        error_log('Fetched quotes: ' . json_encode($quotes)); 

        foreach ($quotes as &$quote) {
            // ...existing code...
        }

        return $quotes;
    }

    /**
     * Fetch a single quote by its ID.
     */
    public function getQuoteById(string $quoteId): ?array
    {
        $quote = $this->findById($quoteId);
        error_log('Fetched quote: ' . json_encode($quote)); 
        return $quote;
    }

    /**
     * Create a new quote.
     */
    public function createQuote(array $quoteData): bool
    {
        return $this->create($quoteData); // Use the inherited create() method
    }

    /**
     * Save (bookmark) a quote for a user.
     */
    public function saveQuote(string $userId, string $quoteId): bool
    {
        // First check if already saved
        if ($this->isQuoteSaved($userId, $quoteId)) {
            // Remove save if already saved
            $sql = "DELETE FROM Booked WHERE user_id = :user_id AND quote_id = :quote_id";
        } else {
            // Add save if not saved
            $sql = "INSERT INTO Booked (user_id, quote_id) VALUES (:user_id, :quote_id)";
        }

        $success = $this->db->execute($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId,
        ]);

        if ($success) {
            return true;
        }
        return false;
    }


    /**
     * Like a quote for a user.
     */
    public function likeQuote(string $userId, string $quoteId): bool
    {
        // First check if already liked
        $sql = "SELECT EXISTS(SELECT 1 FROM Likes WHERE user_id = :user_id AND quote_id = :quote_id) as liked";
        $result = $this->db->query($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ])[0];

        if ($result['liked']) {
            // Remove like if already liked
            $sql = "DELETE FROM Likes WHERE user_id = :user_id AND quote_id = :quote_id";
        } else {
            // Add like if not liked
            $sql = "INSERT INTO Likes (user_id, quote_id) VALUES (:user_id, :quote_id)";
        }

        return $this->db->execute($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId,
        ]);
    }


    public function reportQuote(string $userId, string $quoteId, ?string $reason = null): bool
    {
        // First check if already reported
        $sql = "SELECT EXISTS(SELECT 1 FROM Reports WHERE user_id = :user_id AND quote_id = :quote_id) as reported";
        $result = $this->db->query($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ])[0];

        if ($result['reported']) {
            // Remove report if already reported
            $sql = "DELETE FROM Reports WHERE user_id = :user_id AND quote_id = :quote_id";
        } else {
            // Add report if not reported
            $sql = "INSERT INTO Reports (user_id, quote_id" . ($reason ? ", reason" : "") . ") 
                VALUES (:user_id, :quote_id" . ($reason ? ", :reason" : "") . ")";
        }

        $params = [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ];

        if ($reason && !$result['reported']) {
            $params['reason'] = $reason;
        }

        return $this->db->execute($sql, $params);
    }

    public function getUserInteractions(string $userId, string $quoteId): array
    {
        $sql = "SELECT 
        EXISTS(SELECT 1 FROM Likes WHERE user_id = :user_id AND quote_id = :quote_id) as is_liked,
        EXISTS(SELECT 1 FROM Booked WHERE user_id = :user_id AND quote_id = :quote_id) as is_saved,
        EXISTS(SELECT 1 FROM Reports WHERE user_id = :user_id AND quote_id = :quote_id) as is_reported";

        return $this->db->query($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ])[0] ?? [
            'is_liked' => false,
            'is_saved' => false,
            'is_reported' => false
        ];
    }

    public function isQuoteSaved(string $userId, string $quoteId): bool
    {
        $sql = "SELECT EXISTS(SELECT 1 FROM Booked WHERE user_id = :user_id AND quote_id = :quote_id) as saved";
        $result = $this->db->query($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ])[0];

        return (bool)$result['saved'];
    }

    public function addQuoteToCollection(string $collectionId, string $quoteId): bool
    {
        try {
            $sql = "INSERT INTO Collection_Quotes (collection_id, quote_id) VALUES (:collection_id, :quote_id)";
            return $this->db->execute($sql, [
                'collection_id' => $collectionId,
                'quote_id' => $quoteId,
            ]);
        } catch (\Exception $e) {
            error_log('Error adding quote to collection: ' . $e->getMessage());
            return false;
        }
    }
}
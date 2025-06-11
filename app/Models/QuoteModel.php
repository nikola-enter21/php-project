<?php

namespace App\Models;

use Core\BaseModel;

class QuoteModel extends BaseModel
{
    protected string $table = 'quotes';

    /**
     * Get counts of likes, saves and reports for a quote
     */
    public function getQuoteCounts(string $quoteId): array
    {
        $sql = "SELECT 
            (SELECT COUNT(*) FROM likes WHERE quote_id = :quote_id) as likes_count,
            (SELECT COUNT(*) FROM booked WHERE quote_id = :quote_id) as saves_count,
            (SELECT COUNT(*) FROM reports WHERE quote_id = :quote_id) as reports_count";

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
            // Add counts
            $counts = $this->getQuoteCounts($quote['id']);
            $quote['likes_count'] = (int)$counts['likes_count'];
            $quote['saves_count'] = (int)$counts['saves_count'];
            $quote['reports_count'] = (int)$counts['reports_count'];

            // Add user interactions if user is logged in
            if ($userId) {
                $interactions = $this->getUserInteractions($userId, $quote['id']);
                $quote['is_liked'] = (bool)$interactions['is_liked'];
                $quote['is_saved'] = (bool)$interactions['is_saved'];
                $quote['is_reported'] = (bool)$interactions['is_reported'];
            }
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
            $sql = "DELETE FROM booked WHERE user_id = :user_id AND quote_id = :quote_id";
        } else {
            // Add save if not saved
            $sql = "INSERT INTO booked (user_id, quote_id) VALUES (:user_id, :quote_id)";
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
        $sql = "SELECT EXISTS(SELECT 1 FROM likes WHERE user_id = :user_id AND quote_id = :quote_id) as liked";
        $result = $this->db->query($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ])[0];

        if ($result['liked']) {
            // Remove like if already liked
            $sql = "DELETE FROM likes WHERE user_id = :user_id AND quote_id = :quote_id";
        } else {
            // Add like if not liked
            $sql = "INSERT INTO likes (user_id, quote_id) VALUES (:user_id, :quote_id)";
        }

        return $this->db->execute($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId,
        ]);
    }


    public function reportQuote(string $userId, string $quoteId, ?string $reason = null): bool
    {
        // First check if already reported
        $sql = "SELECT EXISTS(SELECT 1 FROM reports WHERE user_id = :user_id AND quote_id = :quote_id) as reported";
        $result = $this->db->query($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ])[0];

        if ($result['reported']) {
            // Remove report if already reported
            $sql = "DELETE FROM reports WHERE user_id = :user_id AND quote_id = :quote_id";
        } else {
            // Add report if not reported
            $sql = "INSERT INTO reports (user_id, quote_id" . ($reason ? ", reason" : "") . ") 
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
        EXISTS(SELECT 1 FROM likes WHERE user_id = :user_id AND quote_id = :quote_id) as is_liked,
        EXISTS(SELECT 1 FROM booked WHERE user_id = :user_id AND quote_id = :quote_id) as is_saved,
        EXISTS(SELECT 1 FROM reports WHERE user_id = :user_id AND quote_id = :quote_id) as is_reported";

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
        $sql = "SELECT EXISTS(SELECT 1 FROM booked WHERE user_id = :user_id AND quote_id = :quote_id) as saved";
        $result = $this->db->query($sql, [
            'user_id' => $userId,
            'quote_id' => $quoteId
        ])[0];

        return (bool)$result['saved'];
    }

    public function getMostLikedQuotes(int $limit = 10, ?string $userId = null): array
    {
        $sql = "SELECT q.*, COUNT(l.id) as likes_count 
            FROM quotes q 
            LEFT JOIN likes l ON q.id = l.quote_id 
            GROUP BY q.id 
            ORDER BY likes_count DESC 
            LIMIT $limit";

        $quotes = $this->db->query($sql);

        foreach ($quotes as &$quote) {
            $counts = $this->getQuoteCounts($quote['id']);
            $quote['likes_count'] = (int)$counts['likes_count'];
            $quote['saves_count'] = (int)$counts['saves_count'];
            $quote['reports_count'] = (int)$counts['reports_count'];

            if ($userId) {
                $interactions = $this->getUserInteractions($userId, $quote['id']);
                $quote['is_liked'] = (bool)$interactions['is_liked'];
                $quote['is_saved'] = (bool)$interactions['is_saved'];
                $quote['is_reported'] = (bool)$interactions['is_reported'];
            }
        }

        return $quotes;
    }

    public function getReportedQuotes(?string $userId = null): array
    {
        $sql = "SELECT q.*, u.full_name AS full_name, u.id AS user_id, COUNT(r.id) as reports_count 
            FROM quotes q 
            LEFT JOIN reports r ON q.id = r.quote_id 
            LEFT JOIN users u ON q.user_id = u.id
            GROUP BY q.id 
            HAVING COUNT(r.id) > 0
            ORDER BY reports_count DESC";

        $quotes = $this->db->query($sql);

        if (empty($quotes)) {
            return [];
        }

        foreach ($quotes as &$quote) {
            $counts = $this->getQuoteCounts($quote['id']);
            $quote['likes_count'] = (int)$counts['likes_count'];
            $quote['saves_count'] = (int)$counts['saves_count'];
            $quote['reports_count'] = (int)$counts['reports_count'];

            if ($userId) {
                $interactions = $this->getUserInteractions($userId, $quote['id']);
                $quote['is_liked'] = (bool)$interactions['is_liked'];
                $quote['is_saved'] = (bool)$interactions['is_saved'];
                $quote['is_reported'] = (bool)$interactions['is_reported'];
            }
        }

        return $quotes;
    }

    public function addQuoteToCollection(string $collectionId, string $quoteId): bool
    {
        try {
            $sql = "INSERT INTO collection_quotes (collection_id, quote_id) VALUES (:collection_id, :quote_id)";
            return $this->db->execute($sql, [
                'collection_id' => $collectionId,
                'quote_id' => $quoteId,
            ]);
        } catch (\Exception $e) {
            error_log('Error adding quote to collection: ' . $e->getMessage());
            return false;
        }
    }

    public function addAnnotation(string $quoteId, string $userId, string $note): bool
    {
        $sql = "INSERT INTO annotations (quote_id, user_id, note) VALUES (:quote_id, :user_id, :note)";
        return $this->db->execute($sql, [
            'quote_id' => $quoteId,
            'user_id' => $userId,
            'note' => $note,
        ]);
    }

    public function getAnnotationsByQuoteId(string $quoteId): array
    {
        $sql = "SELECT a.note, u.full_name AS user_name, a.created_at 
                FROM annotations a 
                JOIN users u ON a.user_id = u.id 
                WHERE a.quote_id = :quote_id 
                ORDER BY a.created_at DESC";
        return $this->db->query($sql, ['quote_id' => $quoteId]);
    }
}
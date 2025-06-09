<?php

namespace App\Controllers;

use Core\Flash;
use Core\Request;
use Core\Response;
use App\Models\QuoteModel;
use App\Models\LogModel;
use App\Models\CollectionModel;
use Exception;

class QuoteController
{
    private QuoteModel $quoteModel;
    private CollectionModel $collectionModel; 
    private LogModel $logModel;

    public function __construct(QuoteModel $quoteModel, CollectionModel $collectionModel, LogModel $logModel)
    {
        $this->quoteModel = $quoteModel;
        $this->collectionModel = $collectionModel; 
        $this->logModel = $logModel;
    }

    public function likeQuote(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');
        if (!$user) {
            $this->logModel->createLog(null, 'like_quote', 'Failed to like quote: User not logged in');
            $res->json(['success' => false, 'message' => 'You must be logged in to like a quote.'], 401);
            return;
        }

        $quoteId = $req->param('id');
        if (!$quoteId || !$this->quoteModel->getQuoteById($quoteId)) {
            $this->logModel->createLog($user['id'], 'like_quote', "Failed to like quote: Invalid quote ID $quoteId");
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        $liked = $this->quoteModel->likeQuote($user['id'], $quoteId);

        if ($liked) {
            // Get updated counts
            $counts = $this->quoteModel->getQuoteCounts($quoteId);
            $interactions = $this->quoteModel->getUserInteractions($user['id'], $quoteId);

            $this->logModel->createLog($user['id'], 'like_quote', "Quote $quoteId like status updated successfully!");
            
            $res->json([
                'success' => true,
                'message' => 'Quote like status updated successfully!',
                'likes_count' => $counts['likes_count'],
                'is_liked' => $interactions['is_liked']
            ]);
        } else {
            $this->logModel->createLog($user['id'], 'like_quote', "Failed to change like status for quote $quoteId");
            $res->json(['success' => false, 'message' => 'Failed to update like status. Please try again.'], 500);
        }
    }

    public function addToCollection(Request $req, Response $res)
    {
        $user = $req->session()->get('user');
        if (!$user) {
            $this->logModel->createLog(null, 'add_to_collection', 'Failed to add quote to collection: User not logged in');
            $res->json(['success' => false, 'message' => 'You must be logged in to add a quote to a collection.'], 401);
            return;
        }

        $collectionId = $req->body('collection_id');
        $quoteId = $req->body('quote_id');

        if (empty($collectionId) || empty($quoteId)) {
            $this->logModel->createLog($user['id'], 'add_to_collection', 'Failed to add quote to collection: Missing collection or quote ID');
            $res->json(['success' => false, 'message' => 'Invalid collection or quote ID.'], 400);
            return;
        }

        $quote = $this->quoteModel->getQuoteById($quoteId);
        if (!$quote) {
            $this->logModel->createLog($user['id'], 'add_to_collection', "Failed to add quote to collection: Quote not found with ID $quoteId");
            $res->json(['success' => false, 'message' => 'Quote not found.'], 404);
            return;
        }

        $added = $this->collectionModel->addQuoteToCollection($collectionId, $quoteId);

        if ($added) {
            $this->logModel->createLog($user['id'], 'add_to_collection', "Quote $quoteId added to collection $collectionId successfully.");
            $res->json(['success' => true, 'message' => 'Quote added to collection successfully.']);
        } else {
            $this->logModel->createLog($user['id'], 'add_to_collection', "Failed to add quote $quoteId to collection $collectionId: Quote already exists in the collection.");
            $res->json(['success' => false, 'message' => 'Quote already exists in the collection.']);
        }
    }

    public function saveQuote(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');

        // Get and validate quote ID
        $quoteId = $req->param('id');
        if (!$quoteId || !$this->quoteModel->getQuoteById($quoteId)) {
            $this->logModel->createLog($user['id'], 'save_quote', "Failed to save quote: Invalid quote ID $quoteId");
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        // Attempt to toggle save status
        $saved = $this->quoteModel->saveQuote($user['id'], $quoteId);

        if ($saved) {
            // Get updated counts and status
            $counts = $this->quoteModel->getQuoteCounts($quoteId);
            $isSaved = $this->quoteModel->isQuoteSaved($user['id'], $quoteId);

            $this->logModel->createLog($user['id'], 'save_quote', "Quote $quoteId save status updated!");
            
            $message = $isSaved ? 'Quote saved successfully!' : 'Quote unsaved successfully!';
            $res->json([
                'success' => true,
                'message' => $message,
                'saves_count' => $counts['saves_count'],
                'is_saved' => $isSaved
            ]);

            $this->logModel->createLog($user['id'], 'save_quote', $message . " for quote $quoteId");

        } else {
            $this->logModel->createLog($user['id'], 'save_quote', "Failed to update save status for quote $quoteId");
            $res->json([
                'success' => false,
                'message' => 'Failed to update save status. Please try again.'
            ], 500);
        }
    }

    public function reportQuote(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');

        // Get and validate quote ID
        $quoteId = $req->param('id');
        if (!$quoteId || !$this->quoteModel->getQuoteById($quoteId)) {
            $this->logModel->createLog($user['id'], 'report_quote', "Failed to report quote: Invalid quote ID $quoteId");
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        // Attempt to toggle report status
        $reported = $this->quoteModel->reportQuote($user['id'], $quoteId);

        if ($reported) {
            // Get updated counts and status
            $counts = $this->quoteModel->getQuoteCounts($quoteId);
            $interactions = $this->quoteModel->getUserInteractions($user['id'], $quoteId);

            $this->logModel->createLog($user['id'], 'report_quote', "Quote $quoteId report status updated successfully!");
            $res->json([
                'success' => true,
                'message' => 'Quote report status updated successfully!',
                'reports_count' => $counts['reports_count'],
                'is_reported' => $interactions['is_reported']
            ]);
        } else {
            $this->logModel->createLog($user['id'], 'report_quote', "Failed to update report status for quote $quoteId");
            $res->json([
                'success' => false,
                'message' => 'Failed to update report status. Please try again.'
            ], 500);
        }
    }

    public function create(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');
        $title = trim($req->body('title'));
        $content = trim($req->body('content'));
        $author = trim($req->body('author'));

        // Validate input
        if (empty($title) || empty($content) || empty($author)) {
            $this->logModel->createLog($user['id'], 'create_quote', 'Failed to create quote: All fields are required');
            $res->json(['success' => false, 'message' => 'All fields are required.']);
            return;
        }

        if (strlen($title) > 255) {
            $this->logModel->createLog($user['id'], 'create_quote', 'Failed to create quote: Title exceeds 255 characters');
            $res->json(['success' => false, 'message' => 'Title must be less than 255 characters.']);
            return;
        }

        // Create the quote
        $created = $this->quoteModel->createQuote([
            'user_id' => $user['id'],
            'title' => $title,
            'content' => $content,
            'author' => $author ?: 'Anonymous'
        ]);

        if ($created) {
            $this->logModel->createLog($user['id'], 'create_quote', "Quote created successfully: Title '$title'");
            $res->json(['success' => true, 'message' => 'Quote created successfully!']);
        } else {
            $this->logModel->createLog($user['id'], 'create_quote', "Failed to create quote: Title '$title'");
            $res->json(['success' => false, 'message' => 'Failed to create quote. Please try again.']);
        }
    }

    public function getQuoteDetails(Request $req, Response $res): void
    {
        $quoteId = $req->param('id');
        $quote = $this->quoteModel->getQuoteById($quoteId);

        if ($quote) {
            $res->json(['success' => true, 'quote' => $quote]);
        } else {
            $res->json(['success' => false, 'message' => 'Quote not found.'], 404);
        }
    }

    public function createView(Request $req, Response $res): void
    {
        $res->view('quotes/create');
    }

    public function deleteQuote(Request $req, Response $res): void
    {
        echo "Delete Quote";
        $user = $req->session()->get('user');
        $quoteId = $req->param('id');
        $quote = $this->quoteModel->getQuoteById($quoteId);

        if (!$quoteId || !$quote) {
            $this->logModel->createLog($user['id'], 'delete_quote', "Failed to delete quote: Invalid quote ID $quoteId");
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        if ($user['role'] !== 'admin' && $user['id'] !== $quote['user_id']) {
            $this->logModel->createLog($user['id'], 'delete_quote', "Unauthorized delete attempt for quote ID: $quoteId");
            $res->json(['success' => false, 'message' => 'You are not authorized to delete this quote.'], 403);
            return;
        }

        $deleted = $this->quoteModel->delete($quoteId);

        if ($deleted) {
            $this->logModel->createLog($user['id'], 'delete_quote', "Quote with ID $quoteId deleted successfully");
            $res->json(['success' => true, 'message' => 'Quote deleted successfully!']);
        } else {
            $this->logModel->createLog($user['id'], 'delete_quote', "Failed to delete quote with ID $quoteId");
            $res->json(['success' => false, 'message' => 'Failed to delete quote. Please try again.'], 500);
        }
    }

    public function addAnnotation(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');

        if (!$user) {
            $res->redirect('/login');
            return;
        }

        $quoteId = $req->param('id');
        if (!$quoteId) {
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        $note = trim($req->body('note') ?? '');

        if (empty($note)) {
            $res->json(['success' => false, 'message' => 'Annotation cannot be empty.'], 400);
            return;
        }

        try {
            $added = $this->quoteModel->addAnnotation($quoteId, $user['id'], $note);

            if ($added) {
                $res->json(['success' => true, 'message' => 'Annotation added successfully!']);
            } else {
                $res->json(['success' => false, 'message' => 'Failed to add annotation. Please try again.'], 500);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $res->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    public function viewAnnotations(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');

        if (!$user) {
            $res->redirect('/login');
            return;
        }

        $quoteId = $req->param('id');
        $annotations = $this->quoteModel->getAnnotationsByQuoteId($quoteId);

        $res->view('annotations/annotations', ['annotations' => $annotations]);
    }

    public function addAnnotationView(Request $req, Response $res): void
    {
        $quoteId = $req->param('id');
        $res->view('annotations/create', ['quoteId' => $quoteId]);
    }
}
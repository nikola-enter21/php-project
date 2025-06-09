<?php

namespace App\Controllers;

use Core\Flash;
use Core\Request;
use Core\Response;
use App\Models\QuoteModel;
use App\Models\CollectionModel;
use Exception;

class QuoteController
{
    private QuoteModel $quoteModel;
    private CollectionModel $collectionModel;

    public function __construct(QuoteModel $quoteModel, CollectionModel $collectionModel)
    {
        $this->quoteModel = $quoteModel;
        $this->collectionModel = $collectionModel;
    }

    public function likeQuote(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');
        if (!$user) {
            $res->json(['success' => false, 'message' => 'You must be logged in to like a quote.'], 401);
            return;
        }

        $quoteId = $req->param('id');
        if (!$quoteId || !$this->quoteModel->getQuoteById($quoteId)) {
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        $liked = $this->quoteModel->likeQuote($user['id'], $quoteId);

        if ($liked) {
            // Get updated counts
            $counts = $this->quoteModel->getQuoteCounts($quoteId);
            $interactions = $this->quoteModel->getUserInteractions($user['id'], $quoteId);

            $res->json([
                'success' => true,
                'message' => 'Quote like status updated successfully!',
                'likes_count' => $counts['likes_count'],
                'is_liked' => $interactions['is_liked']
            ]);
        } else {
            $res->json(['success' => false, 'message' => 'Failed to update like status. Please try again.'], 500);
        }
    }

    public function addToCollection(Request $req, Response $res)
    {
        $user = $req->session()->get('user');
        if (!$user) {
            $res->json(['success' => false, 'message' => 'You must be logged in to add a quote to a collection.'], 401);
            return;
        }

        $collectionId = $req->body('collection_id');
        $quoteId = $req->body('quote_id');

        if (empty($collectionId) || empty($quoteId)) {
            $res->json(['success' => false, 'message' => 'Invalid collection or quote ID.'], 400);
            return;
        }

        $quote = $this->quoteModel->getQuoteById($quoteId);
        if (!$quote) {
            $res->json(['success' => false, 'message' => 'Quote not found.'], 404);
            return;
        }

        $added = $this->collectionModel->addQuoteToCollection($collectionId, $quoteId);

        if ($added) {
            $res->json(['success' => true, 'message' => 'Quote added to collection successfully.']);
        } else {
            $res->json(['success' => false, 'message' => 'Quote already exists in the collection.']);
        }
    }

    public function saveQuote(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');

        // Get and validate quote ID
        $quoteId = $req->param('id');
        if (!$quoteId || !$this->quoteModel->getQuoteById($quoteId)) {
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        // Attempt to toggle save status
        $saved = $this->quoteModel->saveQuote($user['id'], $quoteId);

        if ($saved) {
            // Get updated counts and status
            $counts = $this->quoteModel->getQuoteCounts($quoteId);
            $isSaved = $this->quoteModel->isQuoteSaved($user['id'], $quoteId);

            $res->json([
                'success' => true,
                'message' => $isSaved ? 'Quote saved successfully!' : 'Quote unsaved successfully!',
                'saves_count' => $counts['saves_count'],
                'is_saved' => $isSaved
            ]);
        } else {
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
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        // Attempt to toggle report status
        $reported = $this->quoteModel->reportQuote($user['id'], $quoteId);

        if ($reported) {
            // Get updated counts and status
            $counts = $this->quoteModel->getQuoteCounts($quoteId);
            $interactions = $this->quoteModel->getUserInteractions($user['id'], $quoteId);

            $res->json([
                'success' => true,
                'message' => 'Quote report status updated successfully!',
                'reports_count' => $counts['reports_count'],
                'is_reported' => $interactions['is_reported']
            ]);
        } else {
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
            $res->json(['success' => false, 'message' => 'All fields are required.']);
            return;
        }

        if (strlen($title) > 255) {
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
            $res->json(['success' => true, 'message' => 'Quote created successfully!']);
        } else {
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
            $res->json(['success' => false, 'message' => 'Invalid quote ID.'], 400);
            return;
        }

        if ($user['role'] !== 'admin' && $user['id'] !== $quote['user_id']) {
            $res->json(['success' => false, 'message' => 'You are not authorized to delete this quote.'], 403);
            return;
        }

        $deleted = $this->quoteModel->delete($quoteId);

        if ($deleted) {
            $res->json(['success' => true, 'message' => 'Quote deleted successfully!']);
        } else {
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
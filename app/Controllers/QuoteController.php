<?php

namespace App\Controllers;

use Core\Flash;
use Core\Request;
use Core\Response;
use App\Models\QuoteModel;

class QuoteController
{
    private QuoteModel $quoteModel;

    public function __construct(QuoteModel $quoteModel)
    {
        $this->quoteModel = $quoteModel;
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


// Similar updates for saveQuote and reportQuote methods
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


    public function createView(Request $req, Response $res): void
    {
        $res->view('quotes/create');
    }
}
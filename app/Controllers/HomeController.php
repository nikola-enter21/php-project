<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\QuoteModel;

class HomeController
{
    private QuoteModel $quoteModel;

    public function __construct(QuoteModel $quoteModel)
    {
        $this->quoteModel = $quoteModel;
    }

    /**
     * Show all quotes and handle the "create quote" form if logged in.
     */
    public function index(Request $req, Response $res): void
    {
        $user = $req->session()->get('user');
        $quotes = $this->quoteModel->getAllQuotes($user ? $user['id'] : null);

        $res->view('home', ['quotes' => $quotes, 'user' => $user]);
    }
}
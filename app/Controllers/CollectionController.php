<?php

namespace App\Controllers;
use App\Models\CollectionModel;
use Request;
use Response;

class CollectionController
{
    protected $collectionModel;

    public function __construct()
    {
        $this->collectionModel = new CollectionModel();
    }

    public function create(Request $req, Response $res)
    {
    $name = $req->body('name');
    if ($this->collection->createCollection($name)) {
        $res->redirect('/collections');
    } else {
        $res->json(['success' => false, 'message' => 'Failed to create collection']);
    }
    }

    // Export the collection as a PDF
    public function exportAsPdf()
    {
        $quotes = $this->collection->getQuotes();
        $html = '<h1>Quote Collection</h1><ul>';
        foreach ($quotes as $quote) {
            $html .= '<li>' . htmlspecialchars($quote->text) . ' - ' . htmlspecialchars($quote->author) . '</li>';
        }
        $html .= '</ul>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('collection.pdf');
    }
}
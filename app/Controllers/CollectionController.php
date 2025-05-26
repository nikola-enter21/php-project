<?php

namespace App\Controllers;
use Core\Flash;
use App\Models\CollectionModel;
use Core\Request;
use Core\Response;

class CollectionController
{
    protected CollectionModel $collectionModel;

    public function __construct(CollectionModel $collectionModel)
    {
        $this->collectionModel = $collectionModel;
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

    public function getCollections(Request $req, Response $res)
    {
        $collections = $this->collectionModel->getAllCollections();
        return $res->json([
            'collections' => $collections
        ]);
    }
}
?>
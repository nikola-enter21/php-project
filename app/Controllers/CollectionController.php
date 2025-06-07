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
        $user = $req->session()->get('user');
        $name = trim($req->body('name') ?? '');
        $description = trim($req->body('description') ?? '');

        if (empty($name)) {
            $res->json(['success' => false, 'message' => 'Collection name is required.']);
            return;
        }

        if (strlen($name) > 255) {
            $res->json(['success' => false, 'message' => 'Collection name must be less than 255 characters.']);
            return;
        }

        if (empty($description)) {
            $res->json(['success' => false, 'message' => 'Description is required.']);
            return;
        }

        $created = $this->collectionModel->createCollection([
            'name' => $name,
            'description' => $description,
            'user_id' => $user['id'] // Associate the collection with the user
        ]);

        if ($created) {
            $res->json([
                'success' => true,
                'message' => 'Collection created successfully!',
                'redirect' => '/collections'
            ]);
        } else {
            $res->json([
                'success' => false,
                'message' => 'Failed to create collection. Please try again.'
            ]);
        }
    }

    public function createView(Request $req, Response $res): void
    {
        $res->view('collections/create');
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
        $collections = $this->collectionModel->getAllCollectionsWithQuotes();
        require_once __DIR__ . '/../Views/collections/collections.php';
    }

    public function getCollectionsJson(Request $req, Response $res): void
    {
        try {
            $user = $req->session()->get('user');
            if (!$user) {
                $res->json(['success' => false, 'message' => 'You must be logged in to view collections.'], 401);
                return;
            }

            $collections = $this->collectionModel->getAllCollections();
            $res->json(['success' => true, 'collections' => $collections]);
        } catch (\Exception $e) {
            error_log('Error fetching collections: ' . $e->getMessage());
            $res->json(['success' => false, 'message' => 'Failed to fetch collections.'], 500);
        }
    }
}
?>
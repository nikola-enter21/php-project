<?php
namespace App\Controllers;
use Core\Flash;
use App\Models\CollectionModel;
use App\Models\LogModel;
use Core\Request;
use Core\Response;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class CollectionController
{
    protected CollectionModel $collectionModel;
    private LogModel $logModel;

    public function __construct(CollectionModel $collectionModel, LogModel $logModel)
    {
        $this->collectionModel = $collectionModel;
        $this->logModel = $logModel;
    }

    public function create(Request $req, Response $res)
    {
        $user = $req->session()->get('user');
        $name = trim($req->body('name') ?? '');
        $description = trim($req->body('description') ?? '');

        if (empty($name)) {
            $this->logModel->createLog($user['id'], 'create_collection', 'Failed to create collection: Name is empty');
            $res->json(['success' => false, 'message' => 'Collection name is required.']);
            return;
        }

        if (strlen($name) > 255) {
            $this->logModel->createLog($user['id'], 'create_collection', 'Failed to create collection: Name exceeds 255 characters');
            $res->json(['success' => false, 'message' => 'Collection name must be less than 255 characters.']);
            return;
        }

        if (empty($description)) {
            $this->logModel->createLog($user['id'], 'create_collection', 'Failed to create collection: Description is empty');
            $res->json(['success' => false, 'message' => 'Description is required.']);
            return;
        }

        $created = $this->collectionModel->createCollection([
            'name' => $name,
            'description' => $description,
            'user_id' => $user['id'] // Associate the collection with the user
        ]);

        if ($created) {
            $this->logModel->createLog($user['id'], 'create_collection', "Collection '$name' created successfully.");
            $res->json([
                'success' => true,
                'message' => 'Collection created successfully!',
                'redirect' => '/collections'
            ]);
        } else {
            $this->logModel->createLog($user['id'], 'create_collection', "Failed to create collection '$name'.");
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

    /**
     * @throws MpdfException
     */
    public function exportAsPdf(Request $req, Response $res)
    {
        $collectionId = $req->param('id');
        $user = $req->session()->get('user');

        if (!$collectionId) {
            $this->logModel->createLog($user['id'], 'export_pdf', 'Failed to export PDF: Collection ID is missing');
            $res->json(['success' => false, 'message' => 'Collection ID is missing.'], 400);
            return;
        }

        $collection = $this->collectionModel->findById($collectionId);
        if (!$collection) {
            $this->logModel->createLog($user['id'], 'export_pdf', "Collection with ID $collectionId not found");
            $res->json(['success' => false, 'message' => 'Collection not found.'], 404);
            return;
        }

        $quotes = $this->collectionModel->getQuotesByCollectionId($collectionId);

        $html = '<h1>' . htmlspecialchars($collection['name']) . '</h1>';
        $html .= '<p>' . nl2br(htmlspecialchars($collection['description'])) . '</p>';
        $html .= '<h2>Quotes:</h2><ul>';
        foreach ($quotes as $quote) {
            $html .= '<li><strong>' . htmlspecialchars($quote['title']) . '</strong><br>' .
                htmlspecialchars($quote['content']) . '<br>' .
                '<em>Author: ' . htmlspecialchars($quote['author']) . '</em></li>';
        }
        $html .= '</ul>';

        $mpdf = new Mpdf(['default_font' => 'dejavusans']); // UTF-8 safe
        $mpdf->WriteHTML($html);

        $cleanName = preg_replace('/[\/:*?"<>|]/', '_', $collection['name']);
        $pdfFileName = $cleanName . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $pdfFileName . '"');
        $mpdf->OutputHttpDownload($pdfFileName);

        exit;
    }

    public function getCollections(Request $req, Response $res)
    {
        $user = $req->session()->get('user');
        $collections = $this->collectionModel->getAllCollectionsWithQuotes($user['id']);
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

            $collections = $this->collectionModel->getAllCollectionsByUserId($user['id']);
            $res->json(['success' => true, 'collections' => $collections]);
        } catch (\Exception $e) {
            error_log('Error fetching collections: ' . $e->getMessage());
            $res->json(['success' => false, 'message' => 'Failed to fetch collections.'], 500);
        }
    }

    public function deleteQuoteFromCollection(Request $req, Response $res)
    {
        $collectionId = $req->param('collectionId');
        $quoteId = $req->param('quoteId');
        $user = $req->session()->get('user');

        if (!$collectionId || !$quoteId) {
            $this->logModel->createLog($user['id'], 'delete_quote', 'Failed to delete quote: Collection ID or Quote ID is missing');
            $res->json(['success' => false, 'message' => 'Collection ID or Quote ID is missing.'], 400);
            return;
        }

        $deleted = $this->collectionModel->deleteQuoteFromCollection($collectionId, $quoteId);

        if ($deleted) {
            $this->logModel->createLog($user['id'], 'delete_quote', "Quote with ID $quoteId deleted from collection ID $collectionId successfully.");
            $res->json(['success' => true, 'message' => 'Quote deleted successfully.']);
        } else {
            $this->logModel->createLog($user['id'], 'delete_quote', "Failed to delete quote with ID $quoteId from collection ID $collectionId.");
            $res->json(['success' => false, 'message' => 'Failed to delete the quote.']);
        }
    }

    public function exportAsCsv(Request $req, Response $res): void
    {
        $collectionId = $req->param('id');
        $user = $req->session()->get('user');

        if (!$collectionId) {
            $this->logModel->createLog($user['id'], 'export_csv', 'Failed to export CSV: Collection ID is missing');
            $res->json(['success' => false, 'message' => 'Collection ID is missing.'], 400);
            return;
        }

        $collection = $this->collectionModel->findById($collectionId);
        if (!$collection) {
            $this->logModel->createLog($user['id'], 'export_csv', "Collection with ID $collectionId not found");
            $res->json(['success' => false, 'message' => 'Collection not found.'], 404);
            return;
        }

        $quotes = $this->collectionModel->getQuotesByCollectionId($collectionId);

        $csvData = [];
        $csvData[] = ['Title', 'Content', 'Author']; // Header row
        foreach ($quotes as $quote) {
            $csvData[] = [
                $quote['title'],
                $quote['content'],
                $quote['author']
            ];
        }

        $cleanName = preg_replace('/[\/:*?"<>|]/', '_', $collection['name']);
        $csvFileName = $cleanName . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        $output = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row, ',', '"', '\\');
        }
        fclose($output);

        exit;
    }

    public function exportAsHtml(Request $req, Response $res): void
    {
        $collectionId = $req->param('id');
        $user = $req->session()->get('user');

        if (!$collectionId) {
            $this->logModel->createLog($user['id'], 'export_html', 'Failed to export HTML: Collection ID is missing');
            $res->json(['success' => false, 'message' => 'Collection ID is missing.'], 400);
            return;
        }

        $collection = $this->collectionModel->findById($collectionId);
        if (!$collection) {
            $this->logModel->createLog($user['id'], 'export_html', "Collection with ID $collectionId not found");
            $res->json(['success' => false, 'message' => 'Collection not found.'], 404);
            return;
        }

        $quotes = $this->collectionModel->getQuotesByCollectionId($collectionId);

        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . preg_replace('/[\/:*?"<>|]/', '_', $collection['name']) . '.html' . '"');

        $output = fopen('php://output', 'w');

        fwrite($output, '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>' . htmlspecialchars($collection['name']) . '</title></head><body>');
        fwrite($output, '<h1>' . htmlspecialchars($collection['name']) . '</h1>');
        fwrite($output, '<p>' . nl2br(htmlspecialchars($collection['description'])) . '</p>');
        fwrite($output, '<h2>Quotes:</h2><ul>');

        foreach ($quotes as $quote) {
            fwrite($output, '<li><strong>' . htmlspecialchars($quote['title']) . '</strong><br>');
            fwrite($output, htmlspecialchars($quote['content']) . '<br>');
            fwrite($output, '<em>Author: ' . htmlspecialchars($quote['author']) . '</em></li>');
        }

        fwrite($output, '</ul></body></html>');

        fclose($output);
    }

    public function exportAsBibtex(Request $req, Response $res): void
    {
        $collectionId = $req->param('id');
        $user = $req->session()->get('user');
    
        if (!$collectionId) {
            $this->logModel->createLog($user['id'], 'export_bibtex', 'Failed to export BibTeX: Collection ID is missing');
            $res->json(['success' => false, 'message' => 'Collection ID is missing.'], 400);
            return;
        }
    
        $collection = $this->collectionModel->findById($collectionId);
        if (!$collection) {
            $this->logModel->createLog($user['id'], 'export_bibtex', "Collection with ID $collectionId not found");
            $res->json(['success' => false, 'message' => 'Collection not found.'], 404);
            return;
        }
    
        $quotes = $this->collectionModel->getQuotesByCollectionId($collectionId);
    
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . preg_replace('/[\/:*?"<>|]/', '_', $collection['name']) . '.bib' . '"');
    
        $output = fopen('php://output', 'w');
    
        fwrite($output, "@Collection{" . htmlspecialchars($collection['name']) . ",\n");
        fwrite($output, "  description = {" . htmlspecialchars($collection['description']) . "},\n");
        fwrite($output, "  quotes = {\n");
    
        foreach ($quotes as $quote) {
            fwrite($output, "    @Quote{\n");
            fwrite($output, "      title = {" . htmlspecialchars($quote['title']) . "},\n");
            fwrite($output, "      content = {" . htmlspecialchars($quote['content']) . "},\n");
            fwrite($output, "      author = {" . htmlspecialchars($quote['author']) . "}\n");
            fwrite($output, "    },\n");
        }
    
        fwrite($output, "  }\n");
        fwrite($output, "}");
    
        fclose($output);
    }

}
?>
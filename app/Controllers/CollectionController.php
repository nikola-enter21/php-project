use App\Models\Quote;
use App\Models\Collection;
use Dompdf\Dompdf;

<?php

namespace App\Controllers;


class CollectionController
{
    protected $collection;

    public function __construct()
    {
        $this->collection = new Collection();
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
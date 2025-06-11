<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections | QuoteShare</title>
    <link rel="stylesheet" href="../../../public/assets/reset.css">
    <link rel="stylesheet" href="../../../public/assets/styles.css">
    <link rel="stylesheet" href="../../../public/assets/nav.css">
    <link rel="stylesheet" href="../../../public/assets/collection.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page-wrapper">
        <?php include __DIR__ . '/../partials/nav.php'; ?>
        <main>
            <div class="collection-create-container">

                <?php if ($flash = $req->session()->get('flash')): ?>
                    <div class="message <?= htmlspecialchars($flash['type']) ?>">
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                    <?php $req->session()->remove('flash'); ?>
                <?php endif; ?>
                <div class="collection-create-container">
                    <a href="/collections/create" class="create-collection-btn">
                        <h1>Create New Collection</h1>
                    </a>
                </div>
            </div>

            <section class="collections-section">
                <h2>📚 My Collections</h2>
                <div class="collections-grid">
                    <?php if (!empty($collections)): ?>
                        <?php foreach ($collections as $collection): ?>
                            <div class="collection-card">
                                <div class="collection-title">
                                    <?= htmlspecialchars($collection['name']) ?>
                                </div>

                                <div class="collection-description">
                                    <?= htmlspecialchars($collection['description'] ?? 'No description available') ?>
                                </div>

                                <div class="collection-meta">
                                    <span>Quotes:</span>
                                    <ul>
                                        <?php foreach ($collection['quotes'] as $quote): ?>
                                            <li class="quote-title" data-quote-id="<?= htmlspecialchars($quote['id'] ?? '') ?>">
                                                <strong><?= htmlspecialchars($quote['title'] ?? 'Untitled') ?></strong><br>
                                                <?= htmlspecialchars($quote['content'] ?? 'No content available') ?><br>
                                                <em>Author: <?= htmlspecialchars($quote['author'] ?? 'Anonymous') ?></em>
                                                <button class="delete-quote-btn" data-quote-id="<?= htmlspecialchars($quote['id'] ?? '') ?>" data-collection-id="<?= htmlspecialchars($collection['id']) ?>">
                                                    Remove Quote
                                                </button>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <button class="export-btn" data-collection-id="<?= htmlspecialchars($collection['id']) ?>">Export</button>
                                <div id="export-popup-<?= htmlspecialchars($collection['id']) ?>" class="popup export-popup" style="display: none;">
                                    <div class="popup-content">
                                        <span class="close-popup">&times;</span>
                                        <h3>Select Export Format</h3>
                                        <ul>
                                            <li><a href="/collections/<?= htmlspecialchars($collection['id']) ?>/export-pdf">Export as PDF</a></li>
                                            <li><a href="/collections/<?= htmlspecialchars($collection['id']) ?>/export-csv">Export as CSV</a></li>
                                            <li><a href="/collections/<?= htmlspecialchars($collection['id']) ?>/export-html">Export as HTML</a></li>
                                            <li><a href="/collections/<?= htmlspecialchars($collection['id']) ?>/export-bibtex">Export as BibTeX</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-collections">No collections found. Create your first collection!</p>
                    <?php endif; ?>
                </div>
            </section>   

            <div id="quote-modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <div id="quote-details"></div>
                </div>
            </div>  
        </main>
        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quoteTitles = document.querySelectorAll('.quote-title');
        const modal = document.getElementById('quote-modal');
        const modalContent = document.getElementById('quote-details');
        const closeModal = document.querySelector('.close-modal');

        quoteTitles.forEach(title => {
            title.addEventListener('click', async function () {
                const quoteId = this.dataset.quoteId; 
                try {
                    const response = await fetch('/quotes/' + quoteId);
                    const data = await response.json();
                    if (data.success) {
                        modalContent.innerHTML = `
                            <h2>${data.quote.title}</h2>
                            <p>${data.quote.content}</p>
                            <p><strong>Author:</strong> ${data.quote.author}</p>
                        `;
                        modal.style.display = 'block';
                    }
                } catch (error) {
                    console.error('Error fetching quote details:', error);
                }
            });
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        const deleteButtons = document.querySelectorAll('.delete-quote-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', async function () {
                const quoteId = this.dataset.quoteId;
                const collectionId = this.dataset.collectionId;

                if (!confirm('Are you sure you want to delete this quote?')) {
                    return;
                }

                try {
                    const response = await fetch(`/collections/${collectionId}/quotes/${quoteId}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        alert('Quote deleted successfully.');
                        this.closest('.quote-title').remove();
                    } else {
                        alert(data.message || 'Failed to delete the quote.');
                    }
                } catch (error) {
                    console.error('Error deleting quote:', error);
                    alert('An error occurred while deleting the quote.');
                }
            });
        });

        const exportButtons = document.querySelectorAll('.export-btn');
        const closeButtons = document.querySelectorAll('.close-popup');

        exportButtons.forEach(button => {
            button.addEventListener('click', function () {
                const popupId = `export-popup-${this.dataset.collectionId}`;
                const popup = document.getElementById(popupId);
                popup.style.display = 'block';
            });
        });

        closeButtons.forEach(button => {
            button.addEventListener('click', function () {
                this.closest('.popup').style.display = 'none';
            });
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.popup-content') && !event.target.classList.contains('export-btn')) {
                document.querySelectorAll('.popup').forEach(popup => {
                    popup.style.display = 'none';
                });
            }
        });
    });
</script>
</body>
</html>
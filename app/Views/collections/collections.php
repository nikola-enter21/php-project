<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quote | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/collection.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="collection-create-container">
    <?php include __DIR__ . '/../partials/nav.php'; ?>

    <?php if ($flash = $req->session()->get('flash')): ?>
        <div class="message <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php $req->session()->remove('flash'); ?>
    <?php endif; ?>

    <a href="/collections/create" class="create-collection-btn">
        <h1>Create New Collection</h1>
    </a>
</div>

<section class="collections-section">
    <h2>📚 Featured Collections</h2>
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
                                </li>
                            <?php endforeach; ?>
                        </ul>
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

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quoteTitles = document.querySelectorAll('.quote-title');
        const modal = document.getElementById('quote-modal');
        const modalContent = document.getElementById('quote-details');
        const closeModal = document.querySelector('.close-modal');

        quoteTitles.forEach(title => {
            title.addEventListener('click', async function () {
                const quoteId = this.dataset.quoteId; // Използваме dataset.quoteId
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
    });
</script>
</body>
</html>
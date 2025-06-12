<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | QuoteShare</title>
    <link rel="stylesheet" href="../../public/assets/reset.css">
    <link rel="stylesheet" href="../../public/assets/styles.css">
    <link rel="stylesheet" href="../../public/assets/nav.css">
    <link rel="stylesheet" href="../../public/assets/home.css">
    <link rel="stylesheet" href="../../public/assets/quotes.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="layout-container">
        <?php require_once './app/Views/partials/nav.php'; ?>

        <main class="main-content home-page">
            <section class="welcome-section">
                <?php if (isset($user)): ?>
                    <div class="welcome-banner">
                        <h1>Welcome back, <?= htmlspecialchars($user['full_name']) ?>!</h1>
                        <p>Discover new quotes or share your inspiration with the world.</p>
                    </div>
                <?php else: ?>
                    <div class="call-to-action">
                        <h1>Discover Inspiring Quotes</h1>
                        <p>Join our community to share and collect your favorite quotes.</p>
                        <div class="login-prompt">
                            <a href="/login" class="btn btn-secondary">Log in</a>
                            <a href="/register" class="btn btn-highlight">Sign up</a>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <section class="quotes-section">
                <div id="custom-prompt-container" class="custom-prompt-container hidden">
                    <div class="custom-prompt">
                        <p id="custom-prompt-message">Are you sure you want to delete this quote?</p>
                        <div class="prompt-actions">
                            <button id="custom-prompt-yes" class="btn btn-primary">Yes</button>
                            <button id="custom-prompt-no" class="btn btn-secondary">No</button>
                        </div>
                    </div>
                </div>
                <h2>💫 Featured Quotes</h2>
                <div class="quotes-grid">
                    <?php if (!empty($quotes)): ?>
                        <?php foreach ($quotes as $quote): ?>
                            <div class="quote-card">
                                <div class="quote-actions-top">
                                    <button class="action-icon love <?= isset($quote['is_liked']) && $quote['is_liked'] ? 'active' : '' ?>"
                                            data-quote-id="<?= $quote['id'] ?>"
                                            title="Love this quote">
                                        <i class="fas fa-heart"></i>
                                        <span class="count"><?= $quote['likes_count'] ?></span>
                                    </button>
                                    <div class="action-icon add-to-collection" data-quote-id="<?= htmlspecialchars($quote['id']) ?>" title="Add to Collection">
                                        <i class="fas fa-folder-plus"></i>
                                    </div>
                                    <button class="action-icon save <?= isset($quote['is_saved']) && $quote['is_saved'] ? 'active' : '' ?>"
                                            data-quote-id="<?= $quote['id'] ?>"
                                            title="Save quote">
                                        <i class="fas fa-bookmark"></i>
                                        <span class="count"><?= $quote['saves_count'] ?></span>
                                    </button>
                                    <button class="action-icon report <?= isset($quote['is_reported']) && $quote['is_reported'] ? 'active' : '' ?>"
                                            data-quote-id="<?= $quote['id'] ?>"
                                            title="Report quote">
                                        <i class="fas fa-flag"></i>
                                        <span class="count"><?= $quote['reports_count'] ?></span>
                                    </button>
                                    <?php if ($user && ($user['role'] === 'admin' || $user['id'] === $quote['user_id'])): ?>
                                        <button class="action-icon delete"
                                                data-quote-id="<?= $quote['id'] ?>"
                                                title="Delete quote">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <p class="quote-content">"<?= htmlspecialchars($quote['content']) ?>"</p>
                                <p class="quote-author">- <?= htmlspecialchars($quote['author']) ?></p>
                                <p class="quote-likes">Likes: <?= $quote['likes_count'] ?></p>
                                <div class="quote-annotation-actions">
                                <?php if (isset($user)): ?>
                                        <a href="/quotes/<?= $quote['id'] ?>/annotations/create" class="btn btn-annotation">➕ Add Annotation</a>
                                        <a href="/quotes/<?= $quote['id'] ?>/annotations" class="btn btn-annotation-secondary">📖 View Annotations</a>
                                    <?php else: ?>
                                        <a href="/login" class="btn btn-annotation">➕ Add Annotation</a>
                                        <a href="/login" class="btn btn-annotation-secondary">📖 View Annotations</a>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($quote['image_path'])): ?>
                                    <div class="quote-image-view">
                                        <button class="btn btn-image-view" data-image="<?= htmlspecialchars($quote['image_path']) ?>">
                                            📷 View Image
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-quotes">No quotes found. Be the first to share one!</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="import-csv-section">
                <h2>📂 Import Quotes</h2>
                <p>Want to add multiple quotes at once? Import them using a CSV file.</p>
                <?php if (isset($user)): ?>
                    <a href="/quotes/import-csv" class="btn btn-primary">Go to Import CSV</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-primary">Go to Import CSV</a>
                <?php endif; ?>
            </section>
        </main>
        <?php require_once './app/views/partials/footer.php'; ?>
    </div>

    <div id="collection-popup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <h3>Select a Collection</h3>
            <ul class="collection-list"></ul>
            <p class="no-collections" style="display: none;">No collections available</p>
        </div>
    </div>

    <div id="message-container" class="message-container" style="display: none;">
        <p id="message-text"></p>
    </div>

    <div id="image-modal" class="image-modal hidden">
        <div class="image-modal-content">
            <span class="image-modal-close">&times;</span>
            <img id="modal-image" src="" alt="Quote Image">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const imageButtons = document.querySelectorAll('.btn-image-view');
            const modal = document.getElementById('image-modal');
            const modalImage = document.getElementById('modal-image');
            const modalClose = document.querySelector('.image-modal-close');

            imageButtons.forEach(button => {
                button.addEventListener('click', () => {
                    modalImage.src = button.getAttribute('data-image');
                    modal.classList.remove('hidden');
                });
            });

            modalClose.addEventListener('click', () => {
                modal.classList.add('hidden');
                modalImage.src = '';
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modalImage.src = '';
                }
            });
        });
    </script>

    <script src="../../public/js/quote-actions.js"></script>
</body>
</html>
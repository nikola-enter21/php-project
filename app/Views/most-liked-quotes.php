<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Liked Quotes | Admin</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/home.css">
    <link rel="stylesheet" href="/public/assets/dashboard.css">
    <link rel="stylesheet" href="/public/assets/quotes.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="layout-container">
        <?php include __DIR__ . '/partials/nav.php'; ?>

        <main class="main-content">
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
                <h1>Top 10 Most Liked Quotes</h1>
                <div class="quotes-list">
                    <?php foreach ($quotes as $quote): ?>
                        <div class="quote-card">
                            <div class="quote-actions-top">
                                <button class="action-icon love <?= isset($quote['is_liked']) && $quote['is_liked'] ? 'active' : '' ?>"
                                        data-quote-id="<?= $quote['id'] ?>"
                                        title="Love this quote">
                                    <i class="fas fa-heart"></i>
                                    <span class="count"><?= $quote['likes_count'] ?></span>
                                </button>
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
                                <?php if ($user['role'] === 'admin' || $user['id'] === $quote['user_id']): ?>
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
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
    <script src="/public/js/quote-actions.js"></script>
</body>
</html>

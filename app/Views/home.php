<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="layout-container">
        <?php include __DIR__ . '/partials/nav.php'; ?>

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
                                </div>

                                <div class="quote-title">
                                    <?= htmlspecialchars($quote['title']) ?>
                                </div>

                                <div class="quote-content">
                                    <?= htmlspecialchars($quote['content']) ?>
                                </div>

                                <div class="author-section">
                                    <div class="author-info">
                                        <div class="author-name">
                                            Author: <?= htmlspecialchars($quote['author'] ?? 'Anonymous') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-quotes">No quotes found. Be the first to share one!</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
    <script src="/public/js/quote-ations.js"></script>
</body>
</html>
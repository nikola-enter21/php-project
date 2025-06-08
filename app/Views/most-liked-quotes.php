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
                <h1>Top 10 Most Liked Quotes</h1>
                <div class="quotes-list">
                    <?php foreach ($quotes as $quote): ?>
                        <div class="quote-card">
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
</body>
</html>

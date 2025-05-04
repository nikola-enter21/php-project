<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout-container">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="main-content home-page">
        <section class="welcome-section">
            <?php if (isset($_SESSION['user'])): ?>
                <h1 class="welcome-title">
                    Welcome back, <?= htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8') ?>!
                </h1>
                <p class="welcome-message">
                    Leave a mark. Read the thoughts that others left behind, or share your own.
                </p>
            <?php else: ?>
                <h1 class="welcome-title">Browse Quotes</h1>
                <p class="welcome-message">Add, Share, Like, Comment</p>
            <?php endif; ?>
        </section>

        <section class="quotes-section">
            <h2 class="section-title">💬 Recent Quotes</h2>
            <div class="quotes-grid">
                <?php
                // Mocked quotes
                $mockQuotes = [
                    ['text' => "If you're reading this, know I loved you even when I couldn't say it.", 'author' => 'Anonymous'],
                    ['text' => "I'm sorry for all the times I was silent when I should've spoken.", 'author' => 'Maya L.'],
                    ['text' => "You meant more to me than I could ever admit while I was here.", 'author' => 'J.T.'],
                    ['text' => "Don't cry because I'm gone. Smile because I finally found peace.", 'author' => 'Unknown'],
                    ['text' => "Thank you for being my light in the dark, even when I didn’t say it enough.", 'author' => 'A Friend'],
                    ['text' => "We never said goodbye, but maybe that was our way of staying forever.", 'author' => 'Eli B.']
                ];

                foreach ($mockQuotes as $quote): ?>
                    <div class="quote-card">
                        <p class="quote-text">“<?= htmlspecialchars($quote['text']) ?>”</p>
                        <p class="quote-author">— <?= htmlspecialchars($quote['author']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
</body>
</html>

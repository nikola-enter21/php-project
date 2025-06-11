<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annotations</title>
    <link rel="stylesheet" href="../../../public/assets/reset.css">
    <link rel="stylesheet" href="../../../public/assets/styles.css">
    <link rel="stylesheet" href="../../../public/assets/nav.css">
    <link rel="stylesheet" href="../../../public/assets/home.css">
    <link rel="stylesheet" href="../../../public/assets/dashboard.css">
    <link rel="stylesheet" href="../../../public/assets/quotes.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="layout-container">
        <?php include __DIR__ . '/../partials/nav.php'; ?>

        <main class="main-content">
            <div class="annotations-container">
                <h1>Annotations</h1>
                <?php if (empty($annotations)): ?>
                    <p>No annotations available for this quote.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($annotations as $annotation): ?>
                            <li>
                                <p><strong><?= htmlspecialchars($annotation['user_name']) ?>:</strong> <?= htmlspecialchars($annotation['note']) ?></p>
                                <p><em><?= $annotation['created_at'] ?></em></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </main>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</body>
</html>
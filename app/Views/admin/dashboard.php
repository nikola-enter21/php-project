<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-path" content="<?= BASE_PATH ?>">
    <title>Admin Dashboard | QuoteShare</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/reset.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/styles.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/nav.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/home.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="layout-container">
        <?php include __DIR__ . '/../partials/nav.php'; ?>

        <main class="main-content home-page">
            <section class="welcome-section">
                    <div class="welcome-banner">
                        <h1>Admin Dashboard</h1>
                    </div>
                    <div class="dashboard-content">
                        <article class="stats">
                            <h2>Total Users: <?= $data['totalUsers'] ?></h2>
                            <h2>Total Quotes: <?= $data['totalQuotes'] ?></h2>
                        </article>
                        <div class="dashboard-cards">
                            <div class="card">
                                <h3>Most Liked Quotes</h3>
                                <p>View the quotes with the highest likes.</p>
                                <a href="<?= BASE_PATH ?>/admin/quotes/most-liked" class="btn btn-primary">View</a>
                            </div>
                            <div class="card">
                                <h3>Reported Quotes</h3>
                                <p>Manage quotes that have been reported.</p>
                                <a href="<?= BASE_PATH ?>/admin/quotes/reported" class="btn btn-primary">Manage</a>
                            </div>
                            <div class="card">
                                <h3>User Management</h3>
                                <p>Manage user roles and permissions.</p>
                                <a href="<?= BASE_PATH ?>/admin/users" class="btn btn-primary">Manage users</a>
                            </div>
                            <div class="card">
                                <h3>Logs</h3>
                                <p>View system logs and activity.</p>
                                <a href="<?= BASE_PATH ?>/admin/logs" class="btn btn-primary">View Logs</a>
                            </div>
                        </div>
                    </div>
            </section>
        </main>
        <?php include __DIR__ . '/../partials/footer.php'; ?>

    </div>
</body>
</html>
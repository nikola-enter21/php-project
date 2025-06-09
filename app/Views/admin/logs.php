<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/home.css">
    <link rel="stylesheet" href="/public/assets/dashboard.css">
    <link rel="stylesheet" href="/public/assets/logs.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="layout-container">
        <?php include __DIR__ . '/../partials/nav.php'; ?>

        <main class="main-content">
            <h1>Activity Logs</h1>
            <form method="GET" action="/admin/logs" class="search-form">
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search logs by action, details, or user">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <p class="search-results-heading">
                <?= !empty($search) 
                    ? "Search results for '" . htmlspecialchars($search) . "'" 
                    : "Showing all logs" ?>
            </p>
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5">No logs found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= htmlspecialchars($log['details']) ?></td>
                                <td><?= htmlspecialchars($log['created_at']) ?></td>
                                <td>
                                    <button class="btn btn-danger delete-log-btn" data-log-id="<?= htmlspecialchars($log['id']) ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
    <script src="/public/js/log-actions.js"></script>
</body>
</html>
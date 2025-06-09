<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/home.css">
    <link rel="stylesheet" href="/public/assets/dashboard.css">
    <link rel="stylesheet" href="/public/assets/users.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div id="popup-container" class="popup-container hidden">
        <div class="popup-message">
            <p id="popup-text">Action completed successfully!</p>
            <button id="popup-close" class="btn btn-primary">Close</button>
        </div>
    </div>
    <div class="layout-container">
        <?php include __DIR__ . '/../partials/nav.php'; ?>

        <main class="main-content">
            <section class="manage-users-section">
                <div id="custom-prompt-container" class="custom-prompt-container hidden">
                    <div class="custom-prompt">
                        <p id="custom-prompt-message">Are you sure you want to proceed?</p>
                        <div class="prompt-actions">
                            <button id="custom-prompt-yes" class="btn btn-primary">Yes</button>
                            <button id="custom-prompt-no" class="btn btn-secondary">No</button>
                        </div>
                    </div>
                </div>
                <h1>Manage Users</h1>
                <form method="GET" action="/admin/users" class="search-form">
                    <div class="search-bar-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search users by name or email" class="search-input">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <p class="search-results-heading">
                    <?= !empty($search) 
                        ? "Search results for '" . htmlspecialchars($search) . "'" 
                        : "Showing all users" ?>
                </p>
                <div class="users-list">
                    <?php if (empty($users)): ?>
                        <p class="no-users-message">No users found.</p>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <div class="user-card">
                                <p><strong>Name:</strong> <span class="user-name"> <?= htmlspecialchars($user['full_name']) ?></span></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                                <div class="user-actions">
                                    <form method="POST" action="/admin/users/<?= $user['id'] ?>/role">
                                        <select class="styled-select" name="role">
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-secondary">Update Role</button>
                                    </form>
                                    <button class="btn btn-danger delete-user-btn" data-user-id="<?= $user['id'] ?>">Delete User</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
    <script src="/public/js/user-actions.js"></script>
</body>
</html>
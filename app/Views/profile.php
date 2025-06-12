<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="./public/assets/styles.css">
    <link rel="stylesheet" href="./public/assets/reset.css">
    <link rel="stylesheet" href="./public/assets/nav.css">
    <link rel="stylesheet" href="./public/assets/home.css">
    <link rel="stylesheet" href="./public/assets/dashboard.css">
    <link rel="stylesheet" href="./public/assets/profile.css">
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
        <?php require_once './app/views/partials/nav.php'; ?>
        <main class="main-content">
            <div class="profile-container">
                <h1 class="profile-title">User Profile</h1>
                <div class="profile-info">
                    <img src="./public/assets/images/default-profile.jpg" alt="Profile Picture" class="profile-picture">
                    <div class="profile-details">
                        <p><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>
                <div class="change-password-section">
                    <h2 class="change-password-title">Change Password</h2>
                    <form action="?path=/users/<?= htmlspecialchars($user['id']) ?>/change-password" method="POST" class="change-password-form">                        <label for="old-password">Old Password:</label>
                        <input type="password" id="old-password" name="oldPassword" required>
                        <label for="new-password">New Password:</label>
                        <input type="password" id="new-password" name="newPassword" required>
                        <label for="confirm-password">Confirm New Password:</label>
                        <input type="password" id="confirm-password" name="confirmPassword" required>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </main>
        <?php require_once './app/views/partials/footer.php'; ?>
    </div>
    <script src="./public/js/profile-actions.js"></script>
</body>
</html>
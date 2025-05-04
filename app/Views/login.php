<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout-container">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="main-content">
        <div class="form-container">
            <h2>Login to QuoteShare</h2>
            <form action="/login" method="POST" class="auth-form">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Login</button>
            </form>
            <p class="form-footer">Don't have an account? <a href="/register">Register here</a></p>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>
</body>
</html>

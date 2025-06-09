<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout-container">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="main-content">
        <div class="form-container">
            <h2>Create Your Account</h2>
            <form id="register-form" class="auth-form" method="post" action="/register">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" required>

                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required minlength="6">

                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required minlength="6">

                <div id="error-message" style="color: red; margin-top: 10px;"></div>
                <div id="success-message" style="color: green; margin-top: 10px; display: none;"></div>
                <button type="submit" id="submit-button">Register</button>
            </form>
            <p class="form-footer">Already have an account? <a href="/login">Login here</a></p>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>



</body>
</html>
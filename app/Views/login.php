<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-path" content="<?= BASE_PATH ?>">
    <title>Login | QuoteShare</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/reset.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/styles.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/assets/nav.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout-container">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="main-content">
        <div class="form-container">
            <h2>Login to QuoteShare</h2>
            <form id="login-form" class="auth-form">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <div id="error-message" style="color: red; margin-top: 10px;"></div>
                <button type="submit">Login</button>
            </form>
            <p class="form-footer">Don't have an account? <a href="<?= BASE_PATH ?>/register">Register here</a></p>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('login-form');
        const errorDiv = document.getElementById('error-message');
        const basePath = document.querySelector('meta[name="base-path"]').content;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errorDiv.textContent = '';

            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            fetch(`${basePath}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(async (response) => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        window.location.href = `${basePath}/`;
                    } else {
                        errorDiv.textContent = data.message || 'Login failed. Please try again.';
                    }
                })
                .catch(error => {
                    errorDiv.textContent = `Error: ${error.message || 'Something went wrong.'}`;
                });
        });
    });
</script>

</body>
</html>
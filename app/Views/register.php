<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | QuoteShare</title>
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
            <form id="register-form" class="auth-form">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" required>

                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <div id="error-message" style="color: red; margin-top: 10px;"></div>
                <button type="submit">Register</button>
            </form>
            <p class="form-footer">Already have an account? <a href="/login">Login here</a></p>
        </div>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('register-form');
        const errorDiv = document.getElementById('error-message');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errorDiv.textContent = '';

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                confirm_password: document.getElementById('confirm_password').value
            };

            fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(async (response) => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        window.location.href = '/';
                    } else {
                        errorDiv.textContent = data.message || 'Registration failed. Please try again.';
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

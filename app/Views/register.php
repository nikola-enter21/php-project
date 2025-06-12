<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | QuoteShare</title>
    <link rel="stylesheet" href="../../public/assets/reset.css">
    <link rel="stylesheet" href="../../public/assets/styles.css">
    <link rel="stylesheet" href="../../public/assets/nav.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout-container">
    <?php require_once './app/views/partials/nav.php'; ?>

    <main class="main-content">
        <div class="form-container">
            <h2>Create Your Account</h2>
            <form id="register-form" class="auth-form">
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

    <?php require_once './app/views/partials/footer.php'; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('register-form');
        const errorDiv = document.getElementById('error-message');
        const successDiv = document.getElementById('success-message');
        const submitButton = document.getElementById('submit-button');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errorDiv.textContent = '';
            successDiv.style.display = 'none';
            submitButton.disabled = true;
            submitButton.textContent = 'Registering...';

            const formData = {
                name: document.getElementById('name').value.trim(),
                email: document.getElementById('email').value.trim(),
                password: document.getElementById('password').value,
                confirm_password: document.getElementById('confirm_password').value
            };

            // Client-side validation
            if (formData.password !== formData.confirm_password) {
                errorDiv.textContent = 'Passwords do not match.';
                submitButton.disabled = false;
                submitButton.textContent = 'Register';
                return;
            }

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
                        successDiv.textContent = 'Registration successful! Redirecting...';
                        successDiv.style.display = 'block';
                        setTimeout(() => (window.location.href = '/login'), 2000);
                    } else {
                        errorDiv.textContent = data.message || 'Registration failed. Please try again.';
                    }
                })
                .catch(error => {
                    errorDiv.textContent = `Error: ${error.message || 'Something went wrong.'}`;
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Register';
                });
        });
    });
</script>

</body>
</html>
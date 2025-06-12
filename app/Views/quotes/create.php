<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quote | QuoteShare</title>
    <link rel="stylesheet" href="../../../public/assets/reset.css">
    <link rel="stylesheet" href="../../../public/assets/styles.css">
    <link rel="stylesheet" href="../../../public/assets/nav.css">
    <link rel="stylesheet" href="../../../public/assets/create-quote.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout-container">
    <?php require_once './app/views/partials/nav.php'; ?>

    <main class="main-content">
        <div class="quote-create-container">
            <div class="quote-create-header">
                <h1>Create New Quote</h1>
                <p>Share your favorite quote with the world</p>
            </div>

            <form id="quote-form" class="quote-form" enctype="multipart/form-data">
                <div class="form-field">
                    <label for="title">Title</label>
                    <input type="text"
                           id="title"
                           name="title"
                           maxlength="255"
                           value="<?= htmlspecialchars($old['title'] ?? '') ?>"
                           placeholder="Give your quote a title"
                    >
                    <div class="form-field-info">Add a memorable title for your quote</div>
                    <span class="char-count">0/255</span>
                </div>

                <div class="form-field">
                    <label for="content">Quote Content</label>
                    <textarea id="content"
                              name="content"
                              placeholder="Type or paste your quote here"
                    ><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
                    <div class="form-field-info">The main quote text</div>
                </div>

                <div class="form-field">
                    <label for="author">Author</label>
                    <input type="text"
                           id="author"
                           name="author"
                           value="<?= htmlspecialchars($old['author'] ?? '') ?>"
                           placeholder="Who said or wrote this quote?">
                </div>

                <div class="form-field">
                    <label for="image">Optional Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div class="form-field-info">Attach an image to go with your quote</div>
                </div>

                <button type="submit" class="quote-submit-btn">
                    Create Quote
                </button>
            </form>
        </div>
    </main>

    <?php require_once './app/views/partials/footer.php'; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('quote-form');
        const titleInput = document.getElementById('title');
        const submitBtn = form.querySelector('.quote-submit-btn');

        // Character counter for title
        function updateCharCount() {
            const count = titleInput.value.length;
            titleInput.parentElement.querySelector('.char-count').textContent = `${count}/255`;
        }

        titleInput.addEventListener('input', updateCharCount);
        updateCharCount();

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous messages
            document.querySelectorAll('.message').forEach(msg => msg.remove());

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            try {
                const formData = new FormData(form); // handles text inputs + file

                const response = await fetch('/quotes/create', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${response.ok && data?.success ? 'success' : 'error'}`;
                messageDiv.innerHTML = `
                    <span class="message-icon">${response.ok && data?.success ? '✓' : '⚠️'}</span>
                    ${data.message}
                `;
                form.insertAdjacentElement('beforebegin', messageDiv);

                if (data?.success) {
                    form.reset();
                    updateCharCount();

                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2500);
                }

            } catch (error) {
                console.error(error);
            } finally {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    });
</script>

</body>
</html>
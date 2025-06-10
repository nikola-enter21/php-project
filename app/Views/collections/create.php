<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quote | QuoteShare</title>
    <link rel="stylesheet" href="../../../public/assets/reset.css">
    <link rel="stylesheet" href="../../../public/assets/styles.css">
    <link rel="stylesheet" href="../../../public/assets/nav.css">
    <link rel="stylesheet" href="../../../public/assets/create-collection.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page-wrapper">
        <?php include __DIR__ . '/../partials/nav.php'; ?>
        <main>
            <div class="collection-create-header">
                <h1>Create New Collection</h1>
                <p>Organize your favorite quotes into a collection</p>
            </div>

            <form id="collection-form" class="collection-form" method="POST" action="/collections/create">
                <div class="form-field">
                    <label for="name">Collection Name</label>
                    <input type="text"
                        id="name"
                        name="name"
                        maxlength="255"
                        value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                        placeholder="Give your collection a name"
                    >
                    <div class="form-field-info">Add a meaningful name for your collection</div>
                    <span class="char-count">0/255</span>
                </div>
                <div class="form-field">
                    <label for="description">Collection Description</label>
                    <input type="text"   
                        id="description"
                        name="description"
                        value="<?= htmlspecialchars($old['description'] ?? '') ?>"
                        placeholder="Add a description for the collection."
                    >
                </div>
                <button type="submit" class="collection-submit-btn">
                    Create Collection
                </button>
            </form>
        </main>     
    <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
<script>
    
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('collection-form');
        const nameInput = document.getElementById('name');
        const submitBtn = form.querySelector('.collection-submit-btn');

        function updateCharCount() {
            const count = nameInput.value.length;
            nameInput.parentElement.querySelector('.char-count').textContent =
                `${count}/255`;
        }

        nameInput.addEventListener('input', updateCharCount);
        updateCharCount(); 

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const previousMessages = document.querySelectorAll('.message');
            previousMessages.forEach(msg => msg.remove());

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            try {
                const response = await fetch('/collections/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: nameInput.value.trim(),
                        description: document.getElementById('description').value.trim()
                    })
                });

                const data = await response.json();
                console.log(data);

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
                        window.location.href = '/collections';
                    }, 2500);
                }
            } catch (error) {
                console.log(error);
            } finally {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    });
</script>
</body>
</html>
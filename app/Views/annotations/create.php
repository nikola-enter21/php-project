<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quote | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/create-annotation.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page-wrapper">
        <?php include __DIR__ . '/../partials/nav.php'; ?>
        <main>
            <div class="annotation-create-header">
                <h1>Add new annotation</h1>
                <p>How do you feel while reading this quote?</p>
            </div>

            <form id="annotation-form" class="annotation-form">
                <div class="form-field">
                    <label for="note">Note</label>
                    <input type="text"
                        id="note"
                        name="note"
                        value="<?= htmlspecialchars($old['note'] ?? '') ?>"
                        placeholder="Add a note"
                    >
                    <div class="form-field-info">Add a note to the quote</div>
                </div>
                <button type="submit" class="annotation-submit-btn" data-quote-id="<?= htmlspecialchars($quote['id'] ?? '') ?>">
                    Create Annotation
                </button>
            </form>
        </main>     
    <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
<script>
    
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('annotation-form');
        const submitBtn = form.querySelector('.annotation-submit-btn');
        const quoteId = submitBtn.dataset.quoteId;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const previousMessages = document.querySelectorAll('.message');
            previousMessages.forEach(msg => msg.remove());

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.disabled = true;

            try {
                    const response = await fetch(`/quotes/${quoteId}/annotations/create`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            note: document.getElementById('note').value.trim()
                        })
                    });

                    const data = await response.json();

                    if (data?.success) {
                        form.reset();

                        setTimeout(() => {
                            window.location.href = '/';
                        }, 2500);
                    } else {
                        alert(data.message); // Показване на съобщение за грешка
                    }
                } catch (error) {
                    console.error('Error creating annotation:', error);
                    alert(`An error occurred while creating an annotation: ${JSON.stringify(error)}`);
                }
        });
    });
</script>
</body>
</html>
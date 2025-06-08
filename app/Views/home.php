<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | QuoteShare</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout-container">
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <main class="main-content home-page">
        <section class="welcome-section">
            <?php if (isset($user)): ?>
                <div class="welcome-banner">
                    <h1>Welcome back, <?= htmlspecialchars($user['full_name']) ?>!</h1>
                    <p>Discover new quotes or share your inspiration with the world.</p>
                </div>
            <?php else: ?>
                <div class="call-to-action">
                    <h1>Discover Inspiring Quotes</h1>
                    <p>Join our community to share and collect your favorite quotes.</p>
                    <div class="login-prompt">
                        <a href="/login" class="btn btn-secondary">Log in</a>
                        <a href="/register" class="btn btn-highlight">Sign up</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <section class="quotes-section">
            <h2>💫 Featured Quotes</h2>
            <div class="quotes-grid">
                <?php if (!empty($quotes)): ?>
                    <?php foreach ($quotes as $quote): ?>
                        <div class="quote-card">
                            <div class="quote-actions-top">
                                <button class="action-icon love <?= isset($quote['is_liked']) && $quote['is_liked'] ? 'active' : '' ?>"
                                        data-quote-id="<?= $quote['id'] ?>"
                                        title="Love this quote">
                                    <i class="fas fa-heart"></i>
                                    <span class="count"><?= $quote['likes_count'] ?></span>
                                </button>
                                <button class="action-icon save <?= isset($quote['is_saved']) && $quote['is_saved'] ? 'active' : '' ?>"
                                        data-quote-id="<?= $quote['id'] ?>"
                                        title="Save quote">
                                    <i class="fas fa-bookmark"></i>
                                    <span class="count"><?= $quote['saves_count'] ?></span>
                                </button>
                                <button class="action-icon report <?= isset($quote['is_reported']) && $quote['is_reported'] ? 'active' : '' ?>"
                                        data-quote-id="<?= $quote['id'] ?>"
                                        title="Report quote">
                                    <i class="fas fa-flag"></i>
                                    <span class="count"><?= $quote['reports_count'] ?></span>
                                </button>
                                <div class="action-icon add-to-collection" data-quote-id="<?= htmlspecialchars($quote['id']) ?>" title="Add to Collection">
                                    <i class="fas fa-folder-plus"></i>
                                </div>
                            </div>
                            <div class="quote-title">
                                <?= htmlspecialchars($quote['title']) ?>
                            </div>

                            <div class="quote-content">
                                <?= htmlspecialchars($quote['content']) ?>
                            </div>

                            <div class="author-section">
                                <div class="author-info">
                                    <div class="author-name">
                                        Author: <?= htmlspecialchars($quote['author'] ?? 'Anonymous') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No quotes available at the moment. Check back later!</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<div id="collection-popup" class="popup" style="display: none;">
    <div class="popup-content">
        <span class="close-popup">&times;</span>
        <h3>Select a Collection</h3>
        <ul class="collection-list"></ul>
        <p class="no-collections" style="display: none;">No collections available</p>
    </div>
</div>

<div id="message-container" class="message-container" style="display: none;">
    <p id="message-text"></p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const actionButtons = document.querySelectorAll('.action-icon');

        actionButtons.forEach(button => {
            button.addEventListener('click', async function (e) {
                e.preventDefault();

                const quoteId = this.dataset.quoteId;

                let action;
                if (this.classList.contains('love')) {
                    action = 'like';
                } else if (this.classList.contains('save')) {
                    action = 'save';
                } else if (this.classList.contains('report')) {
                    action = 'report';
                } else if (this.classList.contains('add-to-collection')) {
                    // Skip processing here for "Add to Collection" button
                    return;
                }

                try {
                    const response = await fetch(`/quotes/${quoteId}/${action}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.status === 401 || response.status === 403) {
                        window.location.href = '/login';
                        return;
                    }

                    if (data.success) {
                        // Update the count
                        const countSpan = this.querySelector('.count');
                        if (action === 'like') {
                            countSpan.textContent = data.likes_count;
                            this.classList.toggle('active', data.is_liked);
                        } else if (action === 'save') {
                            countSpan.textContent = data.saves_count;
                            this.classList.toggle('active', data.is_saved);
                        } else if (action === 'report') {
                            countSpan.textContent = data.reports_count;
                            this.classList.toggle('active', data.is_reported);
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });


        const addToCollectionButtons = document.querySelectorAll('.add-to-collection');
        const popup = document.getElementById('collection-popup');
        const collectionList = popup.querySelector('.collection-list');
        const closePopup = popup.querySelector('.close-popup');

        addToCollectionButtons.forEach(button => {
            button.addEventListener('click', async function () {
                const quoteId = this.dataset.quoteId;

                popup.style.display = 'block';
                collectionList.innerHTML = ''; 

                try {
                    const response = await fetch('/collections/json');
                    const data = await response.json();

                    if (data.success && data.collections.length > 0) {
                        data.collections.forEach(collection => {
                            const li = document.createElement('li');
                            li.textContent = collection.name;
                            li.dataset.collectionId = collection.id;
                            li.addEventListener('click', async () => {
                                try {
                                    const addResponse = await fetch(`/quotes/${quoteId}/add-to-collection`, {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({
                                            collection_id: collection.id,
                                            quote_id: quoteId
                                        })
                                    });
                                    const addData = await addResponse.json();

                                    if (addData.success) {
                                        alert(addData.message); 
                                        popup.style.display = 'none'; 
                                    } else {
                                        alert(addData.message); 
                                    }
                                } catch (error) {
                                    console.error('Error adding to collection:', error);
                                    alert('An error occurred while adding the quote to the collection. Please try again.');
    
                                }
                            });
                            collectionList.appendChild(li);
                        });
                    } else {
                        const noCollectionsMessage = document.createElement('p');
                        noCollectionsMessage.textContent = 'No collections available.';
                        noCollectionsMessage.style.color = '#64748b';
                        collectionList.appendChild(noCollectionsMessage);
                    }
                } catch (error) {
                    console.error('Error fetching collections:', error);
                    alert('Failed to fetch collections. Please try again.');
                }
            });
        });

        closePopup.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === popup) {
                popup.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
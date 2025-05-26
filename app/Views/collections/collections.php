<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections</title>
    <link rel="stylesheet" href="/public/assets/reset.css">
    <link rel="stylesheet" href="/public/assets/styles.css">
    <link rel="stylesheet" href="/public/assets/nav.css">
    <link rel="stylesheet" href="/public/assets/create-quote.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Collections</h1>
        <button id="create-collection-btn">Create New Collection</button>
    </header>
    <main>
        <ul id="collections-list">
        <?php foreach ($collections as $collection): ?>
            <li>
                <h2><?php echo htmlspecialchars($collection['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($collection['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="/collection/<?php echo urlencode($collection['id']); ?>">View Collection</a>
            </li>
        <?php endforeach; ?>
        </ul>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const collectionsList = document.getElementById("collections-list");
            const createCollectionBtn = document.getElementById("create-collection-btn");
            async function fetchCollections() {
                try {
                    const response = await fetch("/collections");
                    const data = await response.json();
                    if (data.collections) {
                        renderCollections(data.collections);
                    }
                } catch (error) {
                    console.error("Failed to fetch collections:", error);
                }
            }

            function renderCollections(collections) {
                collectionsList.innerHTML = ""; 
                collections.forEach((collection) => {
                    const li = document.createElement("li");
                    li.textContent = collection.name; 
                    collectionsList.appendChild(li);
                });
            }

            createCollectionBtn.addEventListener("click", async () => {
                const collectionName = prompt("Enter the name of the new collection:");
                if (collectionName) {
                    try {
                        const response = await fetch("/collections/create", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({ name: collectionName }),
                        });
                        if (response.ok) {
                            alert("Collection created successfully!");
                            fetchCollections(); 
                        } else {
                            alert("Failed to create collection.");
                        }
                    } catch (error) {
                        console.error("Failed to create collection:", error);
                    }
                }
            });

            fetchCollections();
        });
    </script>
</body>
</html>
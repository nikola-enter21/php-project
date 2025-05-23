<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Collection</title>
</head>
<body>
    <h1>Create a New Collection</h1>
    <form action="/collections/store" method="POST">
        <div>
            <label for="name">Collection Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <button type="submit">Create Collection</button>
    </form>
</body>
</html>

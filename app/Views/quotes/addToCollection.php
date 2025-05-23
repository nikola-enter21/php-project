<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quote to Collection</title>
</head>
<body>
    <h1>Add Quote to Collection</h1>
    <form action="/collections/add-quote" method="POST">
        <input type="hidden" name="quote_id" id="quoteId" value="<?= htmlspecialchars($quoteId) ?>">
        <div>
            <label for="collection">Select Collection:</label>
            <select name="collection_id" id="collection">
                <?php foreach ($collections as $collection): ?>
                    <option value="<?= htmlspecialchars($collection->id) ?>">
                        <?= htmlspecialchars($collection->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Add</button>
    </form>
</body>
</html>
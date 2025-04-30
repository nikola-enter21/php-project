<?php if (isset($name)): ?>
    <h1>Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>!</h1>
<?php endif; ?>
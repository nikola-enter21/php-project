<header class="main-header">
    <nav class="navbar">
        <h1 class="logo"><a href="<?= BASE_PATH ?>/">QuoteShare</a></h1>
        <ul class="nav-links">

            <?php if (isset($_SESSION['user'])): ?>
                <li>
                    <p>
                        <i class="fas fa-user"></i>
                        <?= $_SESSION['user']['full_name'] ?>
                    </p>
                </li>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li>
                        <a class="admin-dashboard-link" href="<?= BASE_PATH ?>/admin/dashboard">
                            <i class="fas fa-puzzle-piece"></i>
                            Admin Dashboard
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= BASE_PATH ?>/quotes/create" class="create-quote-btn">
                        <i class="fas fa-plus"></i> Create Quote
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_PATH ?>/collections" class="collections-btn">
                        <i class="fas fa-folder"></i> Collections
                    </a>
                </li>
                <li>
                    <form action="<?= BASE_PATH ?>/logout" method="POST" class="logout-form">
                        <button type="submit" class="logout-button">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="<?= BASE_PATH ?>/">Home</a></li>
                <li><a href="<?= BASE_PATH ?>/login">Login</a></li>
                <li><a href="<?= BASE_PATH ?>/register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<header class="main-header">
    <nav class="navbar">
        <h1 class="logo"><a href="?path=/">QuoteShare</a></h1>
        <ul class="nav-links">

            <?php if (isset($_SESSION['user'])): ?>
                <li>
                    <a href="?path=/users/<?= htmlspecialchars($_SESSION['user']['id']) ?>" class="profile-btn">
                        <i class="fas fa-user-circle"></i> <?= $_SESSION['user']['full_name']?>
                    </a>
                </li>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li>
                        <a class="admin-dashboard-link" href="?path=/admin/dashboard">
                            <i class="fas fa-puzzle-piece"></i>
                            Admin Dashboard
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="?path=/quotes/create" class="create-quote-btn">
                        <i class="fas fa-plus"></i> Create Quote
                    </a>
                </li>
                <li>
                    <a href="?path=/collections" class="collections-btn">
                        <i class="fas fa-folder"></i> Collections
                    </a>
                </li>
                <li>
                    <form action="?path=/logout" method="POST" class="logout-form">
                        <button type="submit" class="logout-button">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="?path=/">Home</a></li>
                <li><a href="?path=/login">Login</a></li>
                <li><a href="?path=/register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
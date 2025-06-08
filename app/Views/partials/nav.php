<header class="main-header">
    <nav class="navbar">
        <h1 class="logo"><a href="/">QuoteShare</a></h1>
        <ul class="nav-links">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li>
                        <a class="admin-dashboard-link" href="/admin/dashboard">Admin Dashboard</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="/quotes/create" class="create-quote-btn">
                        <i class="fas fa-plus"></i> Create Quote
                    </a>
                </li>
                <li>
                    <form action="/logout" method="POST" class="logout-form">
                        <button type="submit" class="logout-button">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="/">Home</a></li>
                <li><a href="/login">Login</a></li>
                <li><a href="/register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
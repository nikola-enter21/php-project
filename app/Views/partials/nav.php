<header class="main-header">
    <nav class="navbar">
        <h1 class="logo">QuoteShare</h1>
        <ul class="nav-links">
            <?php if (isset($_SESSION['user'])): ?>
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

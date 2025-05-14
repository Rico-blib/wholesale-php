<?php
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>

<!-- ✅ Sticky navbar using sticky-top -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/ecommerce/index.php">SeKo Wholesale</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- ✅ Nav links on the left -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/ecommerce/index.php">Home</a>
                </li>
                <!-- Add more nav links here if needed -->
            </ul>

            <!-- ✅ Search form in the middle -->
            <form class="d-flex me-3" role="search" action="/ecommerce/search.php" method="GET">
                <input class="form-control me-2" type="search" name="query" placeholder="Search products..." />
                <button class="btn btn-light" type="submit">Search</button>
            </form>

            <!-- ✅ Cart and Account menu on the right -->
            <ul class="navbar-nav">
                <li class="nav-item me-3">
                    <a class="nav-link position-relative" href="/ecommerce/cart/cart.php" title="View your cart">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $cartCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="accountDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Account'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li><a class="dropdown-item" href="/ecommerce/auth/login.php">Sign In</a></li>
                            <li><a class="dropdown-item" href="/ecommerce/auth/register.php">Register</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="/ecommerce/user/profile.php">View Profile</a></li>
                            <li><a class="dropdown-item" href="/ecommerce/user/orders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="/ecommerce/user/wishlist.php">Wishlist</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/ecommerce/auth/logout.php">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

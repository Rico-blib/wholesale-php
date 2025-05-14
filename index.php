<?php
session_start();

include('includes/db.php');

// Handle wishlist add/remove actions
if (isset($_POST['action']) && isset($_POST['product_id'])) {
    $action = $_POST['action'];
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        if ($action == 'add') {
            $conn->query("INSERT INTO wishlist (user_id, product_id) VALUES ($userId, $productId)");
        } elseif ($action == 'remove') {
            $conn->query("DELETE FROM wishlist WHERE user_id = $userId AND product_id = $productId");
        }
        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Grains Wholesale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="style.css" />
</head>

<body>

    <?php include('includes/header.php'); ?>



    <!-- 🌟 ENHANCED CAROUSEL 🌟 -->
    <div id="mainCarousel" class="carousel slide carousel-fade mb-5" data-bs-ride="carousel">
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active">
                <div class="carousel-image-wrapper position-relative">
                    <img src="images/banner1.jpeg" class="d-block w-100" style="height: 500px; object-fit: cover;" alt="Banner 1">
                    <div class="carousel-caption d-none d-md-block text-start bg-dark bg-opacity-50 p-4 rounded">
                        <h2 class="fw-bold">Fresh Grains Delivered</h2>
                        <p>Wholesale pricing direct to your store or home.</p>
                        <a href="/ecommerce/products/category.php?id=1" class="btn btn-success mt-2">Shop Now</a>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item">
                <div class="carousel-image-wrapper position-relative">
                    <img src="images/banner2.jpeg" class="d-block w-100" style="height: 500px; object-fit: cover;" alt="Banner 2">
                    <div class="carousel-caption d-none d-md-block text-end bg-dark bg-opacity-50 p-4 rounded">
                        <h2 class="fw-bold">Support Local Farmers</h2>
                        <p>Quality grains sourced from trusted suppliers.</p>
                        <a href="/ecommerce/products/category.php?id=2" class="btn btn-light text-success mt-2">Explore Categories</a>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-item">
                <div class="carousel-image-wrapper position-relative">
                    <img src="images/banner3.jpeg" class="d-block w-100" style="height: 500px; object-fit: cover;" alt="Banner 3">
                    <div class="carousel-caption d-none d-md-block text-center bg-dark bg-opacity-50 p-4 rounded">
                        <h2 class="fw-bold">Fast Delivery Nationwide</h2>
                        <p>Order now and receive your stock in 24-48 hours.</p>
                        <a href="/ecommerce/index.php#featured" class="btn btn-outline-light mt-2">See Featured</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- ✅ Categories -->
    <div class="container mt-5">
        <h2 class="text-center mb-4 fw-bold text-success">Shop by Category</h2>
        <div class="row g-4">
            <?php
            $result = $conn->query("SELECT * FROM categories");
            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 category-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-box-seam fs-2 text-success"></i>
                            </div>
                            <h5 class="card-title fw-semibold">' . htmlspecialchars($row['name']) . '</h5>
                            <a href="products/category.php?id=' . $row['id'] . '" class="btn btn-success mt-3">View Products</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>

    <!-- ✅ Products Section -->
    <div class="container mt-5 mb-5">
        <h2 class="text-center mb-4 fw-bold text-success">Featured Products</h2>
        <div class="row g-4">
            <?php
            $userId = $_SESSION['user_id'] ?? null;
            $wishlistItems = [];

            if ($userId) {
                $wishlistQuery = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $userId");
                while ($wishlistRow = $wishlistQuery->fetch_assoc()) {
                    $wishlistItems[] = $wishlistRow['product_id'];
                }
            }

            $result = $conn->query("SELECT * FROM products LIMIT 8");
            while ($row = $result->fetch_assoc()) {
                $inWishlist = in_array($row['id'], $wishlistItems);
                echo '
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        <!-- Wishlist Button -->
                        <button class="wishlist-btn position-absolute top-0 end-0 m-2 btn btn-light btn-sm rounded-circle"
                                data-product-id="' . $row['id'] . '"
                                data-action="' . ($inWishlist ? 'remove' : 'add') . '"
                                title="' . ($inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist') . '">
                            <i class="bi ' . ($inWishlist ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary') . '"></i>
                        </button>

                        <a href="product.php?id=' . $row['id'] . '" class="text-decoration-none text-dark">
                            <img src="images/' . htmlspecialchars($row['image']) . '" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h6 class="card-title fw-bold">' . htmlspecialchars($row['name']) . '</h6>
                                <p class="text-success fw-semibold">KSh ' . number_format($row['price'], 2) . '</p>
                            </div>
                        </a>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>

    <!-- ✅ Toast for feedback -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="wishlistToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="wishlistToastBody">Action successful.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>

</html>
<?php
session_start();
include('includes/db.php');

// Make sure it matches the form input name="query"
$searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container mt-5">
    <h2 class="fw-bold text-success mb-4">
        Search Results for: 
        <span class="text-dark">
            <?= htmlspecialchars($searchTerm) ?>
        </span>
    </h2>

    <div class="row g-4">
        <?php
        if (!empty($searchTerm)) {
            $stmt = $conn->prepare("
                SELECT p.* FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.name LIKE ? OR c.name LIKE ?
            ");
            $searchLike = "%" . $searchTerm . "%";
            $stmt->bind_param("ss", $searchLike, $searchLike);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $userId = $_SESSION['user_id'] ?? null;
                $wishlistItems = [];

                if ($userId) {
                    $wishlistQuery = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $userId");
                    while ($wishlistRow = $wishlistQuery->fetch_assoc()) {
                        $wishlistItems[] = $wishlistRow['product_id'];
                    }
                }

                while ($row = $result->fetch_assoc()) {
                    $inWishlist = in_array($row['id'], $wishlistItems);

                    echo '
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 shadow-sm border-0 position-relative">
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
            } else {
                echo '<div class="col-12"><p class="text-muted">No products found for "' . htmlspecialchars($searchTerm) . '".</p></div>';
            }
        } else {
            echo '<div class="col-12"><p class="text-muted">Please enter a search term.</p></div>';
        }
        ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>

<?php
session_start();
include('../includes/db.php');

$categoryId = $_GET['id'] ?? null;

if (!$categoryId) {
    echo "Invalid category.";
    exit;
}

// Get category name (optional)
$categoryResult = $conn->query("SELECT name FROM categories WHERE id = $categoryId");
$category = $categoryResult->fetch_assoc();
$categoryName = $category['name'] ?? "Category";

$result = $conn->query("SELECT * FROM products WHERE category_id = $categoryId");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($categoryName) ?> - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h2 class="text-success fw-bold text-center mb-4"><?= htmlspecialchars($categoryName) ?> Products</h2>
    <div class="row g-4">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm border-0">
                        <a href="../product.php?id=' . $row['id'] . '" class="text-decoration-none text-dark">
                            <img src="../images/' . htmlspecialchars($row['image']) . '" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h6 class="fw-bold">' . htmlspecialchars($row['name']) . '</h6>
                                <p class="text-success fw-semibold">KSh ' . number_format($row['price'], 2) . '</p>
                            </div>
                        </a>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12 text-center text-muted">No products found in this category.</div>';
        }
        ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>

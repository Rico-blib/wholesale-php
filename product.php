<?php
session_start();
include('includes/db.php');

// Check if product ID is in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = $_GET['id'];

// Fetch product from database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-center mt-5'>Product not found.</p>";
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($product['name']); ?> | Grains Wholesale</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- ✅ NAVBAR -->
<?php include('includes/header.php'); ?>

<!-- ✅ Product Detail -->
<div class="container mt-5 mb-5">
  <div class="row">
    <div class="col-md-6 text-center">
      <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded shadow-sm" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-height: 400px; object-fit: contain;">
    </div>
    <div class="col-md-6">
      <h2 class="fw-bold text-success"><?php echo htmlspecialchars($product['name']); ?></h2>
      <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
      <h4 class="text-success fw-semibold">KSh <?php echo number_format($product['price'], 2); ?></h4>

      <form action="cart/add.php" method="post" class="mt-4">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">

        <div class="mb-3">
          <label for="quantity" class="form-label fw-semibold">Quantity:</label>
          <input type="number" name="quantity" id="quantity" value="1" min="1" class="form-control" style="width: 100px;">
        </div>

        <button type="submit" class="btn btn-success w-100">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
      </form>
    </div>
  </div>
</div>

<!-- ✅ Footer -->
<footer class="bg-success text-white text-center py-4 mt-5">
  <p class="mb-0">&copy; <?php echo date('Y'); ?> Grains Wholesale. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

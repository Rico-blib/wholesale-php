<?php
session_start();
include('../includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  $_SESSION['success'] = 'Please log in to view your wishlist.';
  header('Location: ../auth/login.php');
  exit;
}

$userId = $_SESSION['user_id'];

// Fetch wishlist items with product details
$stmt = $conn->prepare("
  SELECT p.id, p.name, p.price, p.image
  FROM wishlist w
  JOIN products p ON w.product_id = p.id
  WHERE w.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Wishlist</title>
  <link rel="stylesheet" href="../style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container my-4">
    <h2 class="mb-4">My Wishlist</h2>

    <!-- Toast Messages -->
    <?php if (isset($_SESSION['success'])): ?>
      <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col">
            <div class="card h-100">
              <img src="../images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                <p class="card-text">Price: Ksh <?= number_format($row['price'], 2) ?></p>
                <div class="d-flex justify-content-between">
                  <!-- Remove Button -->
                  <button class="btn btn-sm btn-danger remove-wishlist" data-product-id="<?= $row['id'] ?>">Remove</button>
                  <!-- Add to Cart Button -->
                  <form action="../cart/cart.php" method="post" class="m-0">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" name="add_to_cart" class="btn btn-sm btn-success">Add to Cart</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>You have no items in your wishlist.</p>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // JavaScript to handle AJAX request for removing wishlist items
    document.addEventListener('DOMContentLoaded', function () {
      const removeButtons = document.querySelectorAll('.remove-wishlist');

      removeButtons.forEach(button => {
        button.addEventListener('click', function () {
          const productId = this.getAttribute('data-product-id');

          // Make AJAX request to remove item from wishlist
          fetch('../wishlist/removewishlist.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              // Remove the card element from the DOM
              this.closest('.col').remove();

              // Show success message
              alert(data.message);
            } else {
              // Show error message
              alert(data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the item from the wishlist.');
          });
        });
      });
    });
  </script>
</body>
</html>

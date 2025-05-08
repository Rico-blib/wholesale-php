<?php
session_start();
include('../includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: ../auth/login.php');
  exit();
}

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
  header('Location: ../index.php');
  exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

// Handle address update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address'])) {
  $address = $_POST['address'];
  $region = $_POST['region'];
  $city = $_POST['city'];
  $alt_phone = $_POST['alt_phone'];
  $details = $_POST['details'];

  $update = $conn->prepare("UPDATE users SET address = ?, region = ?, city = ?, additional_phone = ?, additional_details = ? WHERE id = ?");
  $update->bind_param("sssssi", $address, $region, $city, $alt_phone, $details, $user_id);
  $update->execute();

  // Refresh data
  $user_query->execute();
  $user = $user_query->get_result()->fetch_assoc();

  $_SESSION['flash'] = "Address updated!";
}

// Total calculation
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
  $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Checkout - Grains Wholesale</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
</head>

<body>

  <?php include('../includes/header.php'); ?>

  <div class="container mt-5 mb-5">
    <h2 class="text-center text-success fw-bold mb-4">Checkout</h2>

    <?php if (isset($_SESSION['flash'])): ?>
      <div class="alert alert-success"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
    <?php endif; ?>

    <!-- 🟩 Address Update Form -->
    <form method="POST" id="checkoutForm" action="place_order.php" class="mb-4">
      <div class="row">
        <div class="col-md-6">
          <h5 class="text-success d-flex justify-content-between">
            Customer Address
            <button type="button" class="btn btn-sm btn-outline-success" id="editAddressBtn">Edit Address</button>
          </h5>

          <div class="form-group mb-3">
            <label>Full Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" readonly>
          </div>
          <div class="form-group mb-3">
            <label>Phone Number</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
          </div>

          <div id="addressFields" class="collapse show">
            <div class="form-group mb-3">
              <label>Additional Phone Number</label>
              <input type="text" name="alt_phone" class="form-control" value="<?= htmlspecialchars($user['additional_phone']) ?>">
            </div>
            <div class="form-group mb-3">
              <label>Address</label>
              <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>">
            </div>
            <div class="form-group mb-3">
              <label>Region</label>
              <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($user['region']) ?>">
            </div>
            <div class="form-group mb-3">
              <label>City</label>
              <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city']) ?>">
            </div>
            <div class="form-group mb-3">
              <label>Additional Details</label>
              <textarea name="details" class="form-control" rows="2"><?= htmlspecialchars($user['additional_details']) ?></textarea>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-outline-secondary" id="cancelAddressBtn">Cancel</button>
              <button type="submit" name="update_address" class="btn btn-success">Save Address</button>
            </div>
          </div>
        </div>

        <!-- 🟩 Payment & Summary -->
        <div class="col-md-6">
          <h5 class="text-success">Payment Method</h5>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="payment_method" value="Cash on Delivery" required>
            <label class="form-check-label">Cash on Delivery</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="payment_method" value="M-Pesa">
            <label class="form-check-label">M-Pesa</label>
          </div>
          <div class="form-check mb-4">
            <input class="form-check-input" type="radio" name="payment_method" value="Bank Card">
            <label class="form-check-label">Bank Card</label>
          </div>

          <h5 class="text-success">Order Summary</h5>
          <ul class="list-group mb-3">
            <?php foreach ($_SESSION['cart'] as $item): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($item['product_name']) ?> x <?= $item['quantity'] ?>
                <span>KSh <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between fw-bold">
              Total
              <span>KSh <?= number_format($totalPrice, 2) ?></span>
            </li>
          </ul>

          <div class="d-flex justify-content-between">
            <a href="../index.php" class="btn btn-outline-secondary">Continue Shopping</a>
            <button type="submit" id="confirmBtn" name="confirm_order" class="btn btn-success">Confirm Order</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <?php include('../includes/footer.php'); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('editAddressBtn').addEventListener('click', function () {
      const collapse = new bootstrap.Collapse(document.getElementById('addressFields'), { toggle: true });
    });

    document.getElementById('cancelAddressBtn').addEventListener('click', function () {
      const collapse = bootstrap.Collapse.getInstance(document.getElementById('addressFields'));
      if (collapse) collapse.hide();
    });
  </script>
</body>

</html>

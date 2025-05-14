<?php 
session_start();
include('../includes/db.php');

// Redirect to homepage if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: ../index.php');
    exit;
}

// Calculate total cart price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

// Handle removing item from cart
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $removeId) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['flash'] = 'Item removed from cart.';
            header('Location: cart.php');
            exit;
        }
    }
}

// ✅ Fix: Use user_id to check login status
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shopping Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../index.php">Grains Wholesale</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item me-3">
          <a class="nav-link active" href="cart.php">Cart</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
            <?php echo $isLoggedIn ? htmlspecialchars($_SESSION['user_name']) : 'Account'; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
            <?php if ($isLoggedIn): ?>
              <li><a class="dropdown-item" href="../user/orders.php">Orders</a></li>
              <li><a class="dropdown-item" href="../user/wishlist.php">Wishlist</a></li>
              <li><a class="dropdown-item text-danger" href="../auth/logout.php">Logout</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="../auth/login.php">Sign In</a></li>
              <li><a class="dropdown-item" href="../auth/register.php">Register</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>



<!-- ✅ Cart Content -->
<div class="container mt-5 mb-5">
  <h2 class="text-center mb-4 fw-bold text-success">Your Shopping Cart</h2>

  <?php if (isset($_SESSION['flash'])): ?>
    <div id="flash-message" class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo $_SESSION['flash']; unset($_SESSION['flash']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (empty($_SESSION['cart'])): ?>
    <p class="text-center">Your cart is empty. Start shopping <a href="../index.php">here</a>.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-success">
          <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Remove</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($_SESSION['cart'] as $item): ?>
            <tr>
              <td><img src="../images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="height: 50px;"></td>
              <td><?php echo htmlspecialchars($item['product_name']); ?></td>
              <td>
                <form method="post" action="update.php">
                  <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                  <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" style="width: 60px;">
                  <button type="submit" class="btn btn-outline-success btn-sm mt-2 w-100">Update</button>
                </form>
              </td>
              <td>KSh <?php echo number_format($item['price'], 2); ?></td>
              <td>KSh <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
              <td>
                <a href="cart.php?remove=<?php echo $item['product_id']; ?>" class="btn btn-outline-danger btn-sm">
                  <i class="bi bi-trash"></i> Remove
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Total and Checkout -->
      <div class="d-flex justify-content-between align-items-center">
        <h4>Total: KSh <?php echo number_format($totalPrice, 2); ?></h4>
        <a href="<?php echo $isLoggedIn ? 'checkout.php' : '../auth/login.php'; ?>" class="btn btn-success btn-lg">
          Proceed to Checkout
        </a>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- ✅ Footer -->

<?php include('../includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  setTimeout(() => {
    const flash = document.getElementById('flash-message');
    if (flash) {
      flash.classList.remove('show');
      setTimeout(() => flash.remove(), 300);
    }
  }, 3000);
</script>

</body>
</html>

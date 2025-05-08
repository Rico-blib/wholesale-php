<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
  header('Location: ../auth/login.php');
  exit();
}

$user_id = $_SESSION['user_id'];

$all_orders = $conn->prepare("SELECT o.*, oi.*, p.name AS product_name, p.image FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id WHERE o.user_id = ? ORDER BY o.created_at DESC");
$all_orders->bind_param("i", $user_id);
$all_orders->execute();
$result = $all_orders->get_result();

$orders = ["All" => [], "Pending" => [], "Completed" => []];
while ($row = $result->fetch_assoc()) {
  $status = ucfirst($row['status']);
  $orders['All'][] = $row;
  if ($status === 'Pending') $orders['Pending'][] = $row;
  if (in_array($status, ['Completed', 'Confirmed'])) $orders['Completed'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>My Orders - Grains Wholesale</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
  <style>
    .order-card {
      border: 1px solid #dee2e6;
      border-radius: 10px;
      overflow: hidden;
      transition: 0.3s;
    }

    .order-card img {
      width: 100px;
      height: 100px;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <?php include('../includes/header.php'); ?>
  <div class="container my-5">
    <h2 class="text-center text-success mb-4">My Orders</h2>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-3" id="orderTabs">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#all">All</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#pending">Pending</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#completed">Completed</a>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
      <?php foreach (["All", "Pending", "Completed"] as $status): ?>
        <div class="tab-pane fade<?= $status === 'All' ? ' show active' : '' ?>" id="<?= strtolower($status) ?>">
          <?php if (count($orders[$status]) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
              <?php foreach ($orders[$status] as $item): ?>
                <div class="col">
                  <div class="card order-card h-100">
                    <div class="row g-0">
                      <div class="col-auto">
                        <img src="../images/<?= htmlspecialchars($item['image']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($item['product_name']) ?>">
                      </div>
                      <div class="col">
                        <div class="card-body">
                          <h6 class="card-title mb-2">Order #<?= $item['order_id'] ?> - <span class="text-muted small">Status: <?= ucfirst($item['status']) ?></span></h6>
                          <p class="mb-1 fw-semibold"><?= htmlspecialchars($item['product_name']) ?></p>
                          <p class="mb-1">Qty: <?= $item['quantity'] ?> × KSh <?= number_format($item['price'], 2) ?></p>
                          <p class="fw-bold">Total: KSh <?= number_format($item['price'] * $item['quantity'], 2) ?></p>

                          <?php if (ucfirst($item['status']) === 'Pending'): ?>
                            <form action="cancel_order.php" method="POST">
                              <input type="hidden" name="order_id" value="<?= $item['order_id'] ?>">
                              <button type="submit" class="btn btn-danger btn-sm">Cancel Order</button>
                            </form>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="alert alert-info">No <?= $status ?> orders found.</div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php include('../includes/footer.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
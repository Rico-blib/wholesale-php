<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include('../header.php');
include('../../includes/db.php');

// Validate and get user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = "Invalid user ID.";
    header('Location: index.php');
    exit();
}

$user_id = intval($_GET['id']);

// Fetch user info
$user_stmt = $conn->prepare("SELECT name, email, phone, created_at FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    $_SESSION['msg'] = "User not found.";
    header('Location: index.php');
    exit();
}

$user = $user_result->fetch_assoc();

// Fetch user's orders
$order_stmt = $conn->prepare("SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
?>

<div class="container mt-4">
  <h3 class="text-success mb-3">User Details</h3>

  <div class="card mb-4">
    <div class="card-body">
      <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
      <p><strong>Joined:</strong> <?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></p>
      <a href="index.php" class="btn btn-secondary btn-sm mt-2">Back to Users</a>
    </div>
  </div>

  <h4 class="mb-3">Order History</h4>

  <?php if ($order_result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-success">
          <tr>
            <th>Order #</th>
            <th>Total (KSh)</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($order = $order_result->fetch_assoc()): ?>
            <tr>
              <td><?= $order['id'] ?></td>
              <td>KSh <?= number_format($order['total_amount'], 2) ?></td>
              <td>
                <span class="badge bg-<?= $order['status'] == 'Pending' ? 'warning' : ($order['status'] == 'Confirmed' ? 'primary' : ($order['status'] == 'Completed' ? 'success' : 'danger')) ?>">
                  <?= ucfirst($order['status']) ?>
                </span>
              </td>
              <td><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></td>
              <td>
                <a href="../orders/view.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">View</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No orders placed by this user.</div>
  <?php endif; ?>
</div>

<?php include('../footer.php'); ?>

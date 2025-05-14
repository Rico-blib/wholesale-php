<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include('../header.php');
include('../../includes/db.php');

// Get all orders with user details
$sql = "SELECT o.id, o.user_id, o.status, o.total_amount, o.created_at, 
               u.name AS user_name, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2 class="mb-4 text-success">Manage Orders</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-info"><?= $_SESSION['msg'];
                                        unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-success">
                    <tr>
                        <th>Order #</th>
                        <th>User</th>
                        <th>Total (KSh)</th>
                        <th>Status</th>
                        <th>Change Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?><br><small><?= $row['email'] ?></small></td>
                            <td>KSh <?= number_format($row['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $row['status'] == 'Pending' ? 'warning' : ($row['status'] == 'Confirmed' ? 'primary' : ($row['status'] == 'Completed' ? 'success' : 'danger')) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form action="update_status.php" method="POST" class="d-flex">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm me-2">
                                        <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Confirmed" <?= $row['status'] == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="Canceled" <?= $row['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success">Update</button>
                                </form>
                            </td>
                            <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                            <td>
                                <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info mb-1">View</a><br>
                                <?php if (in_array($row['status'], ['Completed', 'Canceled'])): ?>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this order?');">
                                        Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No orders found.</div>
    <?php endif; ?>
</div>

<?php include('../footer.php'); ?>
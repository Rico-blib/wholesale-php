<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include('../header.php');
include('../../includes/db.php');

// Fetch all users
$sql = "SELECT id, name, email, phone, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
  <h2 class="mb-4 text-success">Manage Users</h2>

  <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-info"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-success">
          <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Registered On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
              <td>
                <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this user?');">
                   Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No users found.</div>
  <?php endif; ?>
</div>

<?php include('../footer.php'); ?>

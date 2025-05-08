<?php
include('../header.php');
require_once('../../includes/db.php');

// Fetch admins
$query = $conn->prepare("SELECT * FROM admins ORDER BY created_at DESC");
$query->execute();
$result = $query->get_result();
$admins = $result->fetch_all(MYSQLI_ASSOC);

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Admin Management</h4>
        <a href="add.php" class="btn btn-primary">Add New Admin</a>
    </div>

    <?php if (count($admins) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= $admin['id'] ?></td>
                            <td><?= htmlspecialchars($admin['username']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td><?= ucfirst($admin['role']) ?></td>
                            <td>
                                <span class="badge bg-<?= $admin['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($admin['status']) ?>
                                </span>
                            </td>
                            <td><?= $admin['last_login'] ?: 'Never' ?></td>
                            <td><?= $admin['created_at'] ?></td>
                            <td>
                                <a href="edit.php?id=<?= $admin['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?= $admin['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this admin?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No admins found.</div>
    <?php endif; ?>
</div>

<?php include('../footer.php'); ?>
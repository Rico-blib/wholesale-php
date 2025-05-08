<?php
session_start();
include('../../includes/db.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<?php include('../header.php'); ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-success">All Categories</h2>
        <a href="add.php" class="btn btn-success">Add New Category</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $category['id'] ?></td>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No categories found.</div>
    <?php endif; ?>
</div>

<?php include('../footer.php'); ?>

<?php
session_start();
include('../../includes/db.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch all products
$query = "SELECT p.id, p.name, p.price, p.image, c.name AS category 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          ORDER BY p.id DESC";
$result = $conn->query($query);
?>

<?php include('../header.php'); ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-success">All Products</h2>
        <a href="add.php" class="btn btn-success">Add New Product</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price (KSh)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../../images/<?= htmlspecialchars($product['image']) ?>" width="60" height="60" style="object-fit:cover;"></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td><?= number_format($product['price'], 2) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No products found.</div>
    <?php endif; ?>
</div>

<?php include('../footer.php'); ?>

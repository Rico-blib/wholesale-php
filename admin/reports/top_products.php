<?php
include('../header.php');
include('../../includes/db.php');

$sql = "SELECT p.name AS product_name, SUM(oi.quantity) AS quantity_sold, SUM(oi.quantity * oi.price) AS total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY oi.product_id
        ORDER BY quantity_sold DESC
        LIMIT 10"; // Top 10 products

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>Top Products</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity Sold</th>
                <th>Total Revenue (KSh)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['product_name'] ?></td>
                    <td><?= $row['quantity_sold'] ?></td>
                    <td><?= number_format($row['total_revenue'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('../footer.php'); ?>

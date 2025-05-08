<?php
include('../header.php');
include('../../includes/db.php');

$sql = "SELECT payment_method, SUM(total_amount) AS total_sales
        FROM orders
        GROUP BY payment_method";

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>Sales by Payment Method</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Total Sales (KSh)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['payment_method'] ?></td>
                    <td><?= number_format($row['total_sales'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('../footer.php'); ?>

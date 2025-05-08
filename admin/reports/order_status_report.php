<?php
include('../header.php');
include('../../includes/db.php');

$sql = "SELECT status, COUNT(*) AS order_count
        FROM orders
        GROUP BY status";

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>Order Status Report</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Status</th>
                <th>Order Count</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['order_count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('../footer.php'); ?>

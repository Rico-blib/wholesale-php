<?php
include('../header.php');
include('../../includes/db.php');

// Default date range (last 30 days)
$start_date = date('Y-m-d', strtotime('-30 days'));
$end_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

$sql = "SELECT SUM(total_amount) AS total_sales, payment_method 
        FROM orders 
        WHERE created_at BETWEEN ? AND ? 
        GROUP BY payment_method";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>Sales Summary</h2>

    <form method="POST" class="mb-3">
        <input type="date" name="start_date" value="<?= $start_date ?>" required>
        <input type="date" name="end_date" value="<?= $end_date ?>" required>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

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

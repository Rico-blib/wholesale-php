<?php
// Start session and check for admin login
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include('../header.php');
include('../../includes/db.php');

// If 'download' is set in the URL, generate CSV
if (isset($_GET['download'])) {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=mpesa_payment_logs.csv');
    
    // Open the output stream for CSV
    $output = fopen('php://output', 'w');
    
    // Write column headers
    fputcsv($output, ['Order ID', 'Amount', 'M-Pesa Receipt', 'Phone Used', 'Paid Amount', 'Payment Time']);
    
    // Fetch orders with M-Pesa payment details
    $sql = "SELECT o.id, o.total_amount, o.mpesa_receipt, o.phone_used, o.paid_amount, o.payment_time
            FROM orders o 
            WHERE o.payment_method = 'M-Pesa' AND o.mpesa_receipt IS NOT NULL";
    
    $result = $conn->query($sql);
    
    // Write each row of data to the CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

// Fetch M-Pesa payment logs from the database
$sql = "SELECT o.id, o.user_id, o.total_amount, o.mpesa_receipt, o.paid_amount, o.phone_used, o.payment_time
        FROM orders o 
        WHERE o.payment_method = 'M-Pesa' AND o.mpesa_receipt IS NOT NULL
        ORDER BY o.payment_time DESC";

$result = $conn->query($sql);
?>

<div class="container mt-4">
  <h2 class="mb-4 text-success">M-Pesa Payment Logs</h2>

  <!-- Button to download CSV -->
  <a href="?download=true" class="btn btn-sm btn-primary mb-3">Download CSV</a>

  <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-success">
          <tr>
            <th>Order #</th>
            <th>Amount (KSh)</th>
            <th>M-Pesa Receipt</th>
            <th>Phone Used</th>
            <th>Paid Amount</th>
            <th>Payment Time</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td>KSh <?= number_format($row['total_amount'], 2) ?></td>
              <td><?= htmlspecialchars($row['mpesa_receipt']) ?></td>
              <td><?= htmlspecialchars($row['phone_used']) ?></td>
              <td>KSh <?= number_format($row['paid_amount'], 2) ?></td>
              <td><?= date('Y-m-d H:i', strtotime($row['payment_time'])) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No M-Pesa payment logs found.</div>
  <?php endif; ?>
</div>

<?php include('../footer.php'); ?>

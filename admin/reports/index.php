<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}
include('../header.php');
?>

<div class="container mt-4">
  <h2 class="mb-4 text-success">Reports Dashboard</h2>

  <div class="row row-cols-1 row-cols-md-2 g-4">
    <div class="col">
      <a href="mpesa_logs.php" class="text-decoration-none">
        <div class="card border-success shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title text-success">M-Pesa Logs</h5>
            <p class="card-text">View and download M-Pesa payment logs.</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col">
      <a href="order_status_report.php" class="text-decoration-none">
        <div class="card border-primary shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title text-primary">Order Status Report</h5>
            <p class="card-text">Track how many orders are Pending, Confirmed, Completed, or Canceled.</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col">
      <a href="sales_by_payment_method.php" class="text-decoration-none">
        <div class="card border-info shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title text-info">Sales by Payment Method</h5>
            <p class="card-text">See total sales grouped by payment method (e.g., M-Pesa, Cash).</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col">
      <a href="sales_summary.php" class="text-decoration-none">
        <div class="card border-dark shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title text-dark">Sales Summary</h5>
            <p class="card-text">Get a summary of total sales, revenue, and completed orders.</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col">
      <a href="top_products.php" class="text-decoration-none">
        <div class="card border-warning shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title text-warning">Top Products</h5>
            <p class="card-text">Identify best-selling products by order count.</p>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>

<?php include('../footer.php'); ?>

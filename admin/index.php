<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include('header.php');
include('../includes/db.php');

// Fetch the role of the logged-in admin
$admin_email = $_SESSION['admin_email'] ?? '';
$role = '';

if (!empty($admin_email)) {
    $stmt = $conn->prepare("SELECT role FROM admins WHERE email = ?");
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $role = $row['role'];
    }
}

// Fetch sales data grouped by day (last 7 days)
$salesQuery = "
    SELECT DATE(created_at) as sale_date, SUM(total_amount) as total
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY sale_date ASC
";
$salesResult = mysqli_query($conn, $salesQuery);

$salesDates = [];
$salesTotals = [];

while ($row = mysqli_fetch_assoc($salesResult)) {
    $salesDates[] = $row['sale_date'];
    $salesTotals[] = $row['total'];
}

// Fetch order status counts
$statusQuery = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$statusResult = mysqli_query($conn, $statusQuery);

$orderStatuses = [];
$orderCounts = [];

while ($row = mysqli_fetch_assoc($statusResult)) {
    $orderStatuses[] = $row['status'];
    $orderCounts[] = $row['count'];
}
?>

<!-- Bootstrap Icons CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success">Welcome, Admin</h2>
        <span class="text-muted"><?= date('l, F j, Y h:i A') ?></span>
    </div>

    <div class="row g-4">

        <!-- Orders -->
        <div class="col-md-4">
            <div class="card border-success shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cart-check-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text">Manage customer orders.</p>
                    <a href="orders/index.php" class="btn btn-outline-success w-100">View Orders</a>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-4">
            <div class="card border-success shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam display-4 text-success mb-3"></i>
                    <h5 class="card-title">Products</h5>
                    <p class="card-text">Add, edit, or remove products.</p>
                    <a href="products/index.php" class="btn btn-outline-success w-100">Manage Products</a>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="col-md-4">
            <div class="card border-success shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-tags-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title">Categories</h5>
                    <p class="card-text">Organize product categories.</p>
                    <a href="categories/index.php" class="btn btn-outline-success w-100">View Categories</a>
                </div>
            </div>
        </div>

        <!-- Users -->
        <div class="col-md-4">
            <div class="card border-success shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title">Users</h5>
                    <p class="card-text">View and manage customers.</p>
                    <a href="users/index.php" class="btn btn-outline-success w-100">Manage Users</a>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-md-4">
            <div class="card border-success shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up display-4 text-success mb-3"></i>
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">Sales and performance reports.</p>
                    <a href="reports/index.php" class="btn btn-outline-success w-100">View Reports</a>
                </div>
            </div>
        </div>

        <!-- Admin Management - visible only to superadmin -->
        <?php if ($role === 'superadmin'): ?>
        <div class="col-md-4">
            <div class="card border-success shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-gear display-4 text-success mb-3"></i>
                    <h5 class="card-title">Admin Management</h5>
                    <p class="card-text">Add or manage other admins.</p>
                    <a href="admins/index.php" class="btn btn-outline-success w-100">Manage Admins</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Dashboard Analytics (Charts) -->
    <div class="row mt-5">
        <div class="col-md-6">
            <canvas id="salesChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="orderStatusChart"></canvas>
        </div>
    </div>

</div>

<?php include('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const salesDates = <?= json_encode($salesDates) ?>;
    const salesTotals = <?= json_encode($salesTotals) ?>;

    const orderStatuses = <?= json_encode($orderStatuses) ?>;
    const orderCounts = <?= json_encode($orderCounts) ?>;

    // Sales Chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: salesDates,
            datasets: [{
                label: 'Sales in KES',
                data: salesTotals,
                borderColor: 'green',
                backgroundColor: 'rgba(0,128,0,0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Sales (Last 7 Days)'
                }
            }
        }
    });

    // Order Status Chart
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: orderStatuses,
            datasets: [{
                data: orderCounts,
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Order Status Breakdown'
                }
            }
        }
    });
</script>

<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include('../header.php');
include('../../includes/db.php');

// Validate the ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$orderId = $_GET['id'];

// Fetch order details
$orderQuery = "SELECT o.*, u.name AS user_name, u.email, u.phone 
               FROM orders o
               JOIN users u ON o.user_id = u.id
               WHERE o.id = ?";
$orderStmt = $conn->prepare($orderQuery);
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

if ($orderResult->num_rows === 0) {
    $_SESSION['error'] = "Order not found.";
    header("Location: index.php");
    exit();
}

$order = $orderResult->fetch_assoc();

// Fetch order items
$orderItemsQuery = "SELECT oi.*, p.name AS product_name, p.price
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
$orderItemsStmt = $conn->prepare($orderItemsQuery);
$orderItemsStmt->bind_param("i", $orderId);
$orderItemsStmt->execute();
$orderItemsResult = $orderItemsStmt->get_result();
?>

<div class="container mt-4">
    <h2 class="mb-4 text-success">Order Details - #<?= $order['id'] ?></h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Customer Information</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>

            <h5 class="card-title mt-4">Order Information</h5>
            <p><strong>Status:</strong> <span class="badge bg-<?= $order['status'] == 'Pending' ? 'warning' : ($order['status'] == 'Confirmed' ? 'primary' : ($order['status'] == 'Completed' ? 'success' : 'danger')) ?>"><?= ucfirst($order['status']) ?></span></p>
            <p><strong>Total Price:</strong> KSh <?= number_format($order['total_price'], 2) ?></p>
            <p><strong>Order Date:</strong> <?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></p>
        </div>
    </div>

    <h4 class="mt-4">Ordered Products</h4>
    <?php if ($orderItemsResult->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price (KSh)</th>
                    <th>Total (KSh)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $orderItemsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>KSh <?= number_format($item['price'], 2) ?></td>
                        <td>KSh <?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No products found in this order.</div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="index.php" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>

<?php include('../footer.php'); ?>

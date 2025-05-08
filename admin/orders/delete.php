<?php
session_start();
include('../../includes/db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Validate the order ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: index.php");
    exit();
}

$orderId = $_GET['id'];

// Fetch the order details
$orderQuery = $conn->prepare("SELECT id, status FROM orders WHERE id = ?");
$orderQuery->bind_param("i", $orderId);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

if ($orderResult->num_rows == 0) {
    $_SESSION['error'] = "Order not found.";
    header("Location: index.php");
    exit();
}

$order = $orderResult->fetch_assoc();

// Check if the order status is either Completed or Canceled
if ($order['status'] != 'Completed' && $order['status'] != 'Canceled') {
    $_SESSION['error'] = "You can only delete orders with a 'Completed' or 'Canceled' status.";
    header("Location: index.php");
    exit();
}

// Proceed to delete the order
$deleteOrderQuery = $conn->prepare("DELETE FROM orders WHERE id = ?");
$deleteOrderQuery->bind_param("i", $orderId);

if ($deleteOrderQuery->execute()) {
    $_SESSION['success'] = "Order deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete the order.";
}

header("Location: index.php");
exit();
?>

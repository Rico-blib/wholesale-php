<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = "Invalid user ID.";
    header('Location: index.php');
    exit();
}

$user_id = intval($_GET['id']);

// Check if user has any orders
$order_check = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$order_check->bind_param("i", $user_id);
$order_check->execute();
$order_check->bind_result($order_count);
$order_check->fetch();
$order_check->close();

if ($order_count > 0) {
    $_SESSION['msg'] = "Cannot delete user with existing orders.";
    header("Location: view.php?id=$user_id");
    exit();
}

// Delete user
$delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete_stmt->bind_param("i", $user_id);

if ($delete_stmt->execute()) {
    $_SESSION['msg'] = "User deleted successfully.";
} else {
    $_SESSION['msg'] = "Error deleting user.";
}

header("Location: index.php");

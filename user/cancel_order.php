<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Check if the order exists and belongs to the user
    $order_check = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $order_check->bind_param("ii", $order_id, $user_id);
    $order_check->execute();
    $result = $order_check->get_result();

    if ($result->num_rows > 0) {
        // Update the order status to 'Canceled'
        $update_order = $conn->prepare("UPDATE orders SET status = 'Canceled' WHERE id = ?");
        $update_order->bind_param("i", $order_id);
        $update_order->execute();

        // Redirect back to the orders page
        header('Location: orders.php');
        exit();
    } else {
        echo "Order not found or you do not have permission to cancel it.";
    }
}
?>

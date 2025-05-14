<?php
session_start();
include('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Check if cart is not empty
if (empty($_SESSION['cart'])) {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'] ?? '';

// Validate payment method
$valid_methods = ['Cash on Delivery', 'M-Pesa', 'Bank Card'];
if (!in_array($payment_method, $valid_methods)) {
    $_SESSION['flash'] = "Invalid payment method.";
    header('Location: checkout.php');
    exit();
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Generate secure unique order token
$order_token = bin2hex(random_bytes(16)); // 32-char secure token

// Insert into orders table with order_token
$order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status, created_at, order_token) VALUES (?, ?, ?, 'Pending', NOW(), ?)");
$order_stmt->bind_param("idss", $user_id, $total, $payment_method, $order_token);
$order_stmt->execute();
$order_id = $order_stmt->insert_id;

// Insert each cart item into order_items
$item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($_SESSION['cart'] as $item) {
    $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $item_stmt->execute();
}

// For M-Pesa payment
if ($payment_method === 'M-Pesa') {
    // Send order amount to M-Pesa
    $_SESSION['current_order_token'] = $order_token;

    // Now send the actual amount dynamically to M-Pesa (instead of a hardcoded value)
    header("Location: ../mpesa/stkpush.php?token=$order_token&amount=$total");
    exit();
}

// For other payment methods (Cash on Delivery or Bank Card)
unset($_SESSION['cart']);
$_SESSION['flash'] = "Order placed successfully!";
header('Location: ../user/orders.php');
exit();
?>

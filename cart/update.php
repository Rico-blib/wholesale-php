<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = max(1, intval($_POST['quantity'])); // Prevent 0 or negative quantities

    // Loop through the cart and update the quantity
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id) {
            $item['quantity'] = $quantity;
            $_SESSION['flash'] = "Quantity updated successfully!";
            break;
        }
    }

    // Redirect back to the cart
    header('Location: cart.php');
    exit;
} else {
    // If not POST, redirect to cart
    header('Location: cart.php');
    exit;
}

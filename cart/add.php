<?php
session_start(); // Start the session to use $_SESSION
include('../includes/db.php'); // Include DB to fetch image

// Ensure the necessary data has been posted
if (isset($_POST['product_id']) && isset($_POST['product_name']) && isset($_POST['price']) && isset($_POST['quantity'])) {
    // Get product details from POST
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name']; // Use the correct POST variable
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Fetch image from database using product_id
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // Check if the cart already exists in the session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = []; // Initialize the cart array
    }

    // Check if the product already exists in the cart
    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $product_id) {
            // If the product is found, update the quantity
            $_SESSION['cart'][$key]['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    // If the product was not found, add it as a new item in the cart
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'product_name' => $product_name, // Store product name
            'price' => $price,
            'quantity' => $quantity,
            'image' => $image // Add image to cart
        ];
    }

    // Redirect back to the index page or to the cart page
    header('Location: ../index.php'); // Or redirect to the cart page if needed
    exit();
} else {
    // If the necessary POST data isn't set, redirect to the homepage
    header('Location: ../index.php');
    exit();
}

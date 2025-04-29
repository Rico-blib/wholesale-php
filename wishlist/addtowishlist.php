<?php
session_start();
header('Content-Type: application/json');
include('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please log in to add items to your wishlist.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

// Validate product ID
if ($productId <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product selected.'
    ]);
    exit;
}

// Check if product is already in the wishlist
$stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'status' => 'info',
        'message' => 'Product is already in your wishlist.'
    ]);
    exit;
}

// Insert into wishlist
$stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
$stmt->bind_param("ii", $userId, $productId);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Product added to your wishlist.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add product to wishlist.'
    ]);
}
?>

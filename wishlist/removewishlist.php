<?php
session_start();
header('Content-Type: application/json');
include('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please log in to remove items from your wishlist.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Get product ID from the POST JSON data
$data = json_decode(file_get_contents('php://input'), true);
$productId = isset($data['product_id']) ? (int) $data['product_id'] : 0;

// Validate product ID
if ($productId <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product selected.'
    ]);
    exit;
}

// Delete from wishlist
$stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $userId, $productId);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Product removed from wishlist.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to remove product from wishlist.'
    ]);
}
?>

<?php
session_start();
include('includes/db.php');

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to perform this action.']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $data['action'] ?? '';
$productId = $data['product_id'] ?? 0;

if ($productId <= 0) {
    echo json_encode(['error' => 'Invalid product ID.']);
    exit;
}

if ($action == 'add_to_wishlist') {
    // Check if product is already in wishlist
    $stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Product is already in your wishlist.']);
        exit;
    }

    // Insert into wishlist
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $productId);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Product added to wishlist.']);
    } else {
        echo json_encode(['error' => 'Failed to add product to wishlist.']);
    }
} elseif ($action == 'remove_from_wishlist') {
    // Remove from wishlist
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Product removed from wishlist.']);
    } else {
        echo json_encode(['error' => 'Failed to remove product from wishlist.']);
    }
} else {
    echo json_encode(['error' => 'Invalid action.']);
}
?>

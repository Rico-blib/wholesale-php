<?php
session_start();
include('../../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Check if the product ID is set and fetch the product data
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch the product data to get the image name before deletion
    $query = "SELECT image FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $image = $product['image'];

        // Delete the product from the database
        $deleteQuery = "DELETE FROM products WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param('i', $productId);

        if ($deleteStmt->execute()) {
            // Check if the image exists and delete it from the images folder
            if ($image && file_exists("../../images/" . $image)) {
                unlink("../../images/" . $image);
            }

            $_SESSION['message'] = 'Product deleted successfully!';
        } else {
            $_SESSION['message'] = 'Failed to delete the product.';
        }
    } else {
        $_SESSION['message'] = 'Product not found.';
    }

    // Redirect to the product list page
    header('Location: index.php');
    exit();
} else {
    $_SESSION['message'] = 'No product ID provided.';
    header('Location: index.php');
    exit();
}
 
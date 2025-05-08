<?php
session_start();
include('../../includes/db.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Validate the ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$categoryId = $_GET['id'];

// Check if any products are using this category
$productCheck = $conn->prepare("SELECT id FROM products WHERE category_id = ?");
$productCheck->bind_param("i", $categoryId);
$productCheck->execute();
$productCheck->store_result();

if ($productCheck->num_rows > 0) {
    $_SESSION['error'] = "Cannot delete category: it is assigned to existing products.";
    header("Location: index.php");
    exit();
}

// Delete the category
$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $categoryId);

if ($stmt->execute()) {
    $_SESSION['success'] = "Category deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete category.";
}

header("Location: index.php");
exit();

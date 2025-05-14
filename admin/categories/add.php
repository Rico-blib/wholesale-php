<?php
session_start();
include('../../includes/db.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['name']);

    if (!empty($categoryName)) {
        // Check if category already exists
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $check->bind_param("s", $categoryName);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Category already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $categoryName);
            if ($stmt->execute()) {
                $success = "Category added successfully.";
            } else {
                $error = "Error adding category.";
            }
        }
    } else {
        $error = "Please enter a category name.";
    }
}
?>

<?php include('../header.php'); ?>

<div class="container mt-4">
    <h2 class="text-success mb-3">Add New Category</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <button type="submit" class="btn btn-success">Add Category</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../footer.php'); ?>

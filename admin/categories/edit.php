<?php
session_start();
include('../../includes/db.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$success = "";
$error = "";

// Get category ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$categoryId = $_GET['id'];

// Fetch existing category
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$category = $result->fetch_assoc();

// Update on form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        // Check for duplicate name
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $check->bind_param("si", $name, $categoryId);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Category name already exists.";
        } else {
            $update = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $update->bind_param("si", $name, $categoryId);
            if ($update->execute()) {
                $success = "Category updated successfully.";
                $category['name'] = $name; // update displayed value
            } else {
                $error = "Failed to update category.";
            }
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}
?>

<?php include('../header.php'); ?>

<div class="container mt-4">
    <h2 class="text-success mb-3">Edit Category</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Category</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../footer.php'); ?>

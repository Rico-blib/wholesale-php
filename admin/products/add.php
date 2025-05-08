<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

include('../../includes/db.php');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $category = intval($_POST['category']); // Assuming you're using categories

    // File upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "../../images/"; // ✅ This keeps it consistent with your frontend
        $targetFile = $targetDir . $imageName;

        // Optional: validate file type and size
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowedTypes)) {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Save to database
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, category_id, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsis", $name, $price, $description, $category, $imageName);

            if ($stmt->execute()) {
                $success = "Product added successfully.";
            } else {
                $error = "Database error: " . $conn->error;
            }

            $stmt->close();
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "No image uploaded.";
    }
}
?>

<?php include('../header.php'); ?>

<div class="container mt-4">
    <h2 class="text-success mb-4">Add New Product</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label fw-semibold">Product Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Price</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Category</label>
            <select name="category" class="form-select" required>
                <?php
                $result = $conn->query("SELECT * FROM categories");
                while ($cat = $result->fetch_assoc()) {
                    echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Product Image</label>
            <input type="file" name="image" accept="image/*" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Add Product</button>
    </form>
</div>

<?php include('../footer.php'); ?>

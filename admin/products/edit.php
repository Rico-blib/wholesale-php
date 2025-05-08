<?php
session_start();
include('../../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Check if the product ID is set and fetch product data
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch product data from the database
    $query = "SELECT p.id, p.name, p.description, p.price, p.image, p.category_id, c.name AS category
              FROM products p
              JOIN categories c ON p.category_id = c.id
              WHERE p.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Product not found.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>No product ID provided.</div>";
    exit();
}

// Handle form submission for updating the product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Handle image upload (if new image is selected)
    $image = $product['image']; // Keep the old image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Upload new image to the 'images/' folder
        $imagePath = '../../images/';
        $imageName = time() . '_' . $_FILES['image']['name'];
        $imageFullPath = $imagePath . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imageFullPath)) {
            $image = $imageName; // Update image path in the database
        } else {
            echo "<div class='alert alert-danger'>Failed to upload the image.</div>";
        }
    }

    // Update product data in the database
    $updateQuery = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ssdisi', $name, $description, $price, $category_id, $image, $productId);

    if ($updateStmt->execute()) {
        echo "<div class='alert alert-success'>Product updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update the product.</div>";
    }
}

// Fetch categories for the dropdown
$categoryQuery = "SELECT id, name FROM categories";
$categoryResult = $conn->query($categoryQuery);

?>

<?php include('../header.php'); ?>

<div class="container mt-4">
    <h2 class="text-success mb-4">Edit Product</h2>

    <!-- Product Edit Form -->
    <form action="edit.php?id=<?php echo $product['id']; ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Product Description</label>
            <textarea name="description" id="description" class="form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Product Price (KSh)</label>
            <input type="number" name="price" id="price" class="form-control" value="<?php echo number_format($product['price'], 2); ?>" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Product Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <?php while ($category = $categoryResult->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Product Image (Optional)</label>
            <input type="file" name="image" id="image" class="form-control">
            <?php if ($product['image']): ?>
                <img src="../../images/<?php echo htmlspecialchars($product['image']); ?>" width="100" class="mt-2">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-success">Update Product</button>
    </form>
</div>

<?php include('../footer.php'); ?>

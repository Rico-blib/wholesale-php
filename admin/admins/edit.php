<?php
include('../header.php');
require_once('../../includes/db.php');

// Check if 'id' is set and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $admin_id = $_GET['id'];

    // Fetch the admin details from the database
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);  // Bind the admin ID parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();  // Fetch the admin data

    // Check if admin exists
    if (!$admin) {
        echo '<div class="alert alert-danger">Admin not found.</div>';
        exit;
    }
} else {
    echo '<div class="alert alert-danger">Invalid admin ID.</div>';
    exit;
}

$errors = [];
$success = '';

// Handle form submission for updating admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];
    $new_password_confirm = $_POST['new_password_confirm'];

    // Basic validation
    if (empty($username) || empty($email)) {
        $errors[] = "Username and email are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        } elseif ($new_password !== $new_password_confirm) {
            $errors[] = "Passwords do not match.";
        }
    }

    // Check for existing email and username
    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $admin_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already in use.";
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $admin_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username is already in use.";
        }

        // If no errors, update admin info
        if (empty($errors)) {
            // Update password if a new one is provided
            if (!empty($new_password)) {
                $password = password_hash($new_password, PASSWORD_DEFAULT);
            } else {
                $password = $admin['password']; // Keep current password if not updated
            }

            // Update admin info in the database
            $stmt = $conn->prepare("UPDATE admins SET username = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $username, $email, $password, $role, $status, $admin_id);
            if ($stmt->execute()) {
                $success = "Admin details updated successfully.";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<div class="container mt-4">
    <h4>Edit Admin Details</h4>

    <!-- Display errors or success message -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?= implode('<br>', $errors) ?>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- Admin edit form -->
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($admin['username']) ?>">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($admin['email']) ?>">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select" required>
                <option value="superadmin" <?= $admin['role'] == 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                <option value="admin" <?= $admin['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="editor" <?= $admin['role'] == 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="viewer" <?= $admin['role'] == 'viewer' ? 'selected' : '' ?>>Viewer</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select" required>
                <option value="active" <?= $admin['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $admin['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="mb-3">
            <label>New Password (leave blank to keep current password)</label>
            <input type="password" name="new_password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="new_password_confirm" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Update Admin</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../footer.php'); ?>

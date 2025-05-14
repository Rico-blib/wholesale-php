<?php
session_start();
require_once '../includes/db.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check for matching token
    $stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['is_verified'] == 1) {
            $message = "Your email has already been verified.";
        } else {
            // Update to verified
            $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
            $update->bind_param("i", $user['id']);
            $update->execute();

            $message = "Email verified successfully! You can now log in.";
        }
    } else {
        $message = "Invalid or expired verification token.";
    }
} else {
    $message = "No verification token provided.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-info">
        <?= htmlspecialchars($message) ?>
    </div>
    <a href="login.php" class="btn btn-primary">Go to Login</a>
</div>
</body>
</html>

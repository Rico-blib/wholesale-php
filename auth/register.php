<?php
session_start();
include('../includes/db.php');
require '../includes/send_verification_email.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $phone = trim($_POST["phone"]);
  $dob = $_POST["dob"];
  $password = $_POST["password"];
  $confirm_password = $_POST["confirm_password"];
  $verification_token = bin2hex(random_bytes(16));

  // Validate Email
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format!";
  }

  // Validate Phone
  if (!preg_match("/^\d{10,15}$/", $phone)) {
    $errors[] = "Invalid phone number!";
  }

  // Validate Password
  if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
    $errors[] = "Password must be at least 8 characters, include 1 uppercase letter, 1 number, and 1 special character.";
  }

  if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match!";
  }

  if (empty($errors)) {
    // Check if email or phone already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $errors[] = "Email or phone already exists!";
    } else {
      // Try sending the email first
      if (sendVerificationEmail($email, $verification_token, $name)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, date_of_birth, password, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $dob, $hashed_password, $verification_token);
        if ($stmt->execute()) {
          $_SESSION['success'] = "Registration successful! Check your email to verify your account.";
          header("Location: login.php");
          exit;
        } else {
          $errors[] = "Failed to register. Try again.";
        }
      } else {
        $errors[] = "Could not send verification email. Please contact support.";
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Grains Wholesale</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style.css" />
</head>

<body class="bg-light">
  <?php include('../includes/header.php'); ?>

  <div class="container mt-5">
    <div class="form-container shadow-lg">
      <h2 class="mb-4 text-center">Create Your Account</h2>

      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="card p-4">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Date of Birth</label>
          <input type="date" name="dob" class="form-control" required />
        </div>

        <div class="mb-3 position-relative">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required />
          <button type="button" class="btn btn-sm btn-secondary position-absolute top-50 end-0 translate-middle-y me-2" onclick="togglePassword('password')">Show</button>
        </div>

        <div class="mb-3 position-relative">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" required />
          <button type="button" class="btn btn-sm btn-secondary position-absolute top-50 end-0 translate-middle-y me-2" onclick="togglePassword('confirm_password')">Show</button>
        </div>

        <button class="btn btn-success w-100">Register</button>

        <p class="mt-3 text-center">Already have an account? <a href="login.php">Log in</a></p>
      </form>

    </div>
  </div>

  <?php include('../includes/footer.php'); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      if (field.type === "password") {
        field.type = "text";
      } else {
        field.type = "password";
      }
    }
  </script>

</body>

</html>
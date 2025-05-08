<?php
session_start();
include('../includes/db.php');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $dob = $_POST["dob"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Validate Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    // Validate Phone (basic validation: digits only, length 10-15)
    if (!preg_match("/^\d{10,15}$/", $phone)) {
        $errors[] = "Invalid phone number! It should contain 10-15 digits.";
    }

    // Password Strength Check (at least 8 characters, 1 uppercase, 1 number, 1 special character)
    if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character.";
    }

    // Password Confirmation Check
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    // Check if email or phone already exists in the database
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email or phone already exists!";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, date_of_birth, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $dob, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION["flash"] = "Registration successful. Please log in.";
                header("Location: login.php");
                exit;
            } else {
                $errors[] = "Something went wrong. Try again later.";
            }
        }
    }
}
?>

<!-- HTML REGISTER FORM -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Grains Wholesale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
</head>
<body class="bg-light">

  <!-- Header -->
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

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" required />
        </div>

        <button class="btn btn-success w-100">Register</button>

        <p class="mt-3 text-center">Already have an account? <a href="login.php">Log in</a></p>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <?php include('../includes/footer.php'); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

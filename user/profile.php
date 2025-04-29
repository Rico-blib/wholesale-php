<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ../auth/login.php');
  exit();
}

require_once '../includes/db.php';

// Fetch user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, phone, date_of_birth, address, region, city, additional_phone, additional_details FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $dob, $address, $region, $city, $additional_phone, $additional_details);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>profile - Grains Wholesale</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
</head>

<body>

  <body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-5">
      <h2>Your Profile</h2>
      <div class="card p-4 shadow rounded-4">
        <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($dob) ?></p>
        <hr>
        <h5>Address Details</h5>
        <p><strong>Address:</strong> <?= htmlspecialchars($address) ?></p>
        <p><strong>Region:</strong> <?= htmlspecialchars($region) ?></p>
        <p><strong>City:</strong> <?= htmlspecialchars($city) ?></p>
        <p><strong>Additional Phone:</strong> <?= htmlspecialchars($additional_phone) ?></p>
        <p><strong>Additional Details:</strong> <?= htmlspecialchars($additional_details) ?></p>

        <a href="editprofile.php" class="btn btn-success mt-3">Edit Profile</a>
      </div>
    </div>

  </body>

</html>
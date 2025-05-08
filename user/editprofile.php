<?php
session_start();
include('../includes/db.php');

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Split form values
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $fullName = $firstName . ' ' . $lastName;

    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $additionalPhone = $conn->real_escape_string($_POST['additional_phone']);
    $dob = $conn->real_escape_string($_POST['date_of_birth']);
    $address = $conn->real_escape_string($_POST['address']);
    $region = $conn->real_escape_string($_POST['region']);
    $city = $conn->real_escape_string($_POST['city']);
    $additionalDetails = $conn->real_escape_string($_POST['additional_details']);

    $updateQuery = "
        UPDATE users 
        SET name='$fullName', email='$email', phone='$phone', additional_phone='$additionalPhone',
            date_of_birth='$dob', address='$address', region='$region', city='$city',
            additional_details='$additionalDetails'
        WHERE id=$userId
    ";

    if ($conn->query($updateQuery)) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}

$user = $conn->query("SELECT * FROM users WHERE id=$userId")->fetch_assoc();

// Split full name for form input
$nameParts = explode(' ', $user['name'], 2);
$firstName = $nameParts[0] ?? '';
$lastName = $nameParts[1] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - Grains Wholesale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-success text-white fw-bold text-center rounded-top-4">
                    <h4 class="mb-0">Edit Your Profile</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($firstName) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($lastName) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Additional Phone</label>
                                <input type="text" name="additional_phone" class="form-control" value="<?= htmlspecialchars($user['additional_phone']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($user['date_of_birth']) ?>" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($user['address']) ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Region</label>
                                <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($user['region']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city']) ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Additional Details</label>
                                <textarea name="additional_details" class="form-control" rows="3"><?= htmlspecialchars($user['additional_details']) ?></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="../index.php" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

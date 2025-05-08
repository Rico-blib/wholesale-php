<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = 'cupazuma';
$dbname = 'ecommerce_db';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected to database!";

$result = mysqli_query($conn, "SELECT * FROM categories");

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<br>Category: " . $row['name'];
    }
} else {
    echo "<br>❌ No categories found or query failed.";
}
?>

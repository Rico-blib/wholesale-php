<?php
$host = 'localhost';
$user = 'root';
$password = 'cupazuma'; // Use your root password if you set one
$dbname = 'ecommerce_db';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

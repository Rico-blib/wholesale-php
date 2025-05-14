<?php
session_start();

// Destroy all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: /ecommerce/index.php");
exit();

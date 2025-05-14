<?php
echo "Testing Gmail SMTP Port 587...<br>";

$fp = fsockopen("smtp.gmail.com", 587, $errno, $errstr, 10);
if (!$fp) {
    echo "❌ Unable to connect to Gmail SMTP: $errstr ($errno)";
} else {
    echo "✅ Successfully connected to smtp.gmail.com on port 587!";
    fclose($fp);
}
?>

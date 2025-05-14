<?php
session_start();
require_once '../includes/db.php';

if (!isset($_GET['token'])) {
    die("Order token missing.");
}

$order_token = $_GET['token'];
$_SESSION['current_order_token'] = $order_token;

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_token = ?");
$stmt->bind_param("s", $order_token);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Invalid order.");
}

$raw_amount = $order['total_amount'];
$amount = (int) round($raw_amount); // Ensure clean integer value
if ($amount < 1) {
    die("Invalid amount for STK Push.");
}

// Fetch user's phone number
$user_id = $order['user_id'];
$user_stmt = $conn->prepare("SELECT phone FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user || empty($user['phone'])) {
    die("Phone number not found for this user.");
}

// Clean and format phone
$phone = preg_replace('/\D/', '', $user['phone']);
if (strpos($phone, '0') === 0) {
    $phone = '254' . substr($phone, 1);
} elseif (strpos($phone, '254') !== 0) {
    $phone = '254' . $phone; // Just in case user entered 7XXXXXXXX
}

// ✅ Log cleaned amount and phone
file_put_contents('debug_inputs.log', json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'raw_amount' => $raw_amount,
    'final_amount' => $amount,
    'raw_phone' => $user['phone'],
    'formatted_phone' => $phone,
    'order_token' => $order_token
], JSON_PRETTY_PRINT), FILE_APPEND);

// Credentials
$shortcode = '174379';
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$timestamp = date('YmdHis');
$password = base64_encode($shortcode . $passkey . $timestamp);

// Access token
require_once '../mpesa/access_token.php';
$access_token = generateAccessToken();

if (!$access_token) {
    die("Access token missing.");
}

$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$data = [
    'BusinessShortCode' => $shortcode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortcode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://6125-102-0-9-158.ngrok-free.app/ecommerce/mpesa/callback.php?token=' . $order_token,
    'AccountReference' => 'GrainsWholesale',
    'TransactionDesc' => 'Order Payment'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log response
file_put_contents('stkpush_log.json', json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'response' => $response,
    'http_code' => $http_code,
    'order_token' => $order_token
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), FILE_APPEND);

// Success or failure
if ($http_code == 200) {
    $response_data = json_decode($response, true);
    $checkout_request_id = $response_data['CheckoutRequestID'] ?? 'Unknown';
    file_put_contents('stkpush_request_id_log.json', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'CheckoutRequestID' => $checkout_request_id,
        'order_token' => $order_token
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), FILE_APPEND);

    header("Location: ../cart/checkout.php?status=initiated&order_token=$order_token");
    exit();
} else {
    $_SESSION['flash'] = "Payment initiation failed. Please try again later.";
    header("Location: ../cart/checkout.php?status=failed&order_token=$order_token");
    exit();
}

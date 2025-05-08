<?php
require_once '../includes/db.php';

// Read and log raw input for debugging
$data = file_get_contents('php://input');
file_put_contents('callback_raw.json', $data . PHP_EOL, FILE_APPEND);

// Decode JSON input
$callback = json_decode($data, true);
$order_token = $_GET['token'] ?? null;

// Validate input
if (!$order_token || !$callback || !isset($callback['Body']['stkCallback'])) {
    http_response_code(400);
    echo json_encode(["ResultDesc" => "Invalid request"]);
    exit();
}

$stkCallback = $callback['Body']['stkCallback'];
$resultCode = $stkCallback['ResultCode'];
$resultDesc = $stkCallback['ResultDesc'] ?? '';

// If payment was successful
if ($resultCode == 0) {
    $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];

    // Extract specific metadata values
    $metadata = [];
    foreach ($callbackMetadata as $item) {
        if (isset($item['Name']) && isset($item['Value'])) {
            $metadata[$item['Name']] = $item['Value'];
        }
    }

    $amount = $metadata['Amount'] ?? null;
    $mpesaReceipt = $metadata['MpesaReceiptNumber'] ?? null;
    $phone = $metadata['PhoneNumber'] ?? null;
    $transactionDate = $metadata['TransactionDate'] ?? null;

    // Update order as confirmed and store payment info
    $stmt = $conn->prepare("UPDATE orders SET status = 'Confirmed', mpesa_receipt = ?, paid_amount = ?, phone_used = ?, payment_time = NOW() WHERE order_token = ?");
    $stmt->bind_param("sdss", $mpesaReceipt, $amount, $phone, $order_token);
    $stmt->execute();

    // Log successful transaction
    file_put_contents('callback_success.log', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'order_token' => $order_token,
        'amount' => $amount,
        'receipt' => $mpesaReceipt,
        'phone' => $phone,
        'transaction_date' => $transactionDate
    ], JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

} else {
    // If payment failed, mark the order as failed
    $stmt = $conn->prepare("UPDATE orders SET status = 'Failed' WHERE order_token = ?");
    $stmt->bind_param("s", $order_token);
    $stmt->execute();

    file_put_contents('callback_failed.log', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'order_token' => $order_token,
        'result_code' => $resultCode,
        'result_desc' => $resultDesc
    ], JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
}

http_response_code(200);
echo json_encode(["ResultDesc" => $resultDesc]);

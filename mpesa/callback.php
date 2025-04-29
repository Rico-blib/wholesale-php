<?php
// Log callback for testing
$callbackJSONData = file_get_contents('php://input');
file_put_contents('mpesa_callback.log', $callbackJSONData, FILE_APPEND);

// Parse and extract data
$data = json_decode($callbackJSONData, true);
if (isset($data['Body']['stkCallback'])) {
    $resultCode = $data['Body']['stkCallback']['ResultCode'];
    $checkoutRequestID = $data['Body']['stkCallback']['CheckoutRequestID'];

    // 0 = success
    if ($resultCode == 0) {
        // Save order as COMPLETED in your database here
        // You can access amount, phone, transaction code, etc.
        $amount = $data['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $mpesaReceipt = $data['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
        $phone = $data['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

        // Example: Update orders set status='Completed' where checkout_id = $checkoutRequestID
    }
}

<?php
date_default_timezone_set('Africa/Nairobi');

// Replace these with your actual sandbox credentials
$consumerKey = ' 0jPoRWeeCahIAxxgahiPNRwmKVJmb2XwviVoihTwL3FpZhl9';
$consumerSecret = 'fpAX0tmGykOBu9PKQ8XEVEkZtvMiikxHCve75UtqXXGwtNATfS2bl2tXToglnxKI';
$BusinessShortCode = '174379';
$Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

// Customer details – retrieve from your form/session
$phone = '254757117791'; // test phone number
$amount = 100;
$accountReference = 'GrainsWholesale';
$transactionDesc = 'Order Payment';
$callbackUrl = ' https://2176-102-0-9-158.ngrok-free.app/mpesa/callback.php'; // Ngrok public URL

// Generate access token
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$accessToken = json_decode($response)->access_token;

// Generate timestamp and password
$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode . $Passkey . $timestamp);

// STK Push request
$stkPushUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$curl_post_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackUrl,
    'AccountReference' => $accountReference,
    'TransactionDesc' => $transactionDesc
];

$ch = curl_init($stkPushUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
$response = curl_exec($ch);
curl_close($ch);

// Show response
echo $response;
?>

<?php
function generateAccessToken() {
    // Use your actual base64-encoded consumer key and secret
    $credentials = 'MGpQb1JXZWVDYWhJQXh4Z2FoaVBOUndtS1ZKbWIyWHd2aVZvaWhUd0wzRnBaaGw5OmZwQVgwdG1HeWtPQnU5UEtROFhFVkVrWnR2TWlpa3hIQ3ZlNzVVdHFYWEd3dE5BVGZTMmJsMnRYVG9nbG54S0k=';
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $credentials
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

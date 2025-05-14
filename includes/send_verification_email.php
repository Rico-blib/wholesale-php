<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Make sure SendGrid is installed via Composer
use Dotenv\Dotenv;
use SendGrid\Mail\Mail;
// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function sendVerificationEmail($toEmail, $token, $name)
{
    $email = new Mail();

    $email->setFrom($_ENV['FROM_EMAIL'], $_ENV['FROM_NAME']);
    $email->setSubject("Verify Your Email Address");
    $email->addTo($toEmail, $name);

    $verificationLink = "http://localhost/ecommerce/auth/verify.php?token=$token";

    $htmlContent = "
        <h3>Hello $name,</h3>
        <p>Thanks for registering. Click the link below to verify your email:</p>
        <a href='$verificationLink'>Verify Email</a>
    ";

    $email->addContent("text/plain", "Thanks for registering. Verify your email: $verificationLink");
    $email->addContent("text/html", $htmlContent);

    // Use secure API key from .env
    $sendgrid = new \SendGrid($_ENV['SENDGRID_API_KEY']);

    try {
        $response = $sendgrid->send($email);

        if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
            return true;
        } else {
            error_log("SendGrid failed: " . $response->statusCode() . " " . $response->body());
            return false;
        }
    } catch (Exception $e) {
        error_log('SendGrid Exception: ' . $e->getMessage());
        return false;
    }
}

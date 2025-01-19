<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer classes
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Prepare JSON response
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    // Setup PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = ''; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = ''; // Replace with no-reply email
        $mail->Password = ''; // Replace with no-reply email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email settings
        $mail->setFrom('EMAIL', 'NAME'); // Replace with no-reply email
        $mail->addAddress(''); // Replace with your email
        $mail->Subject = "Contact Form: $subject";
        
        // Format email body
        $emailBody = "<html><body>";
        $emailBody .= "<h2>Contact Form Submission</h2>";
        $emailBody .= "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>";
        $emailBody .= "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
        $emailBody .= "<p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>";
        $emailBody .= "<div style='padding: 15px; border: 1px solid #ccc; background-color: #f9f9f9; margin-top: 10px;'>";
        $emailBody .= "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";
        $emailBody .= "</div>";
        $emailBody .= "</body></html>";

        $mail->isHTML(true);
        $mail->Body = $emailBody;

        // Send email
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Failed to send message. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>


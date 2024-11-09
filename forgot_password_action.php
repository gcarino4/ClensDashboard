<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer if using Composer

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "colens";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email exists in the members table
    $stmt = $conn->prepare("SELECT id, member_id FROM members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email exists, generate a reset token
        $token = bin2hex(random_bytes(50)); // Generate a random token
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expiry (1 hour)

        // Save the token and expiry time in the database
        $stmt = $conn->prepare("UPDATE members SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send reset link via email
        $resetLink = "https://colenstech.online/CLENSADMINDASHBOARD/clensadmindashboard/reset_password.php?token=" . $token;

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';  // Set the SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'resetpassword@colenstech.online'; // SMTP username
            $mail->Password = 'resetPassword@12345'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('resetpassword@colenstech.online', 'CoLens System');
            $mail->addAddress($email); // Add the recipient email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = 'Click the link below to reset your password:<br><a href="' . $resetLink . '">' . $resetLink . '</a>';

            // Send the email
            $mail->send();

            echo 'Password reset link has been sent to your email address.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "Email address not found.";
    }

    $stmt->close();
}

$conn->close();
?>
<?php
include 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload Composer dependencies
require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password === $confirm_password) {
        // Hash the password for security
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $sql = "UPDATE members SET password = ? WHERE member_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $member_id);

        if ($stmt->execute()) {
            // Fetch user's email from the database
            $email_query = "SELECT email FROM members WHERE member_id = ?";
            $email_stmt = $conn->prepare($email_query);
            $email_stmt->bind_param("i", $member_id);
            $email_stmt->execute();
            $email_stmt->bind_result($email);
            $email_stmt->fetch();
            $email_stmt->close();

            // Set up PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.hostinger.com';  // Hostinger SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'resetpassword@colenstech.online';  // Your Hostinger email address
                $mail->Password = 'resetPassword@12345';  // Your Hostinger email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // SSL encryption
                $mail->Port = 465;  // Port for SSL

                // Recipients
                $mail->setFrom('resetpassword@colenstech.online');
                $mail->addAddress($email);  // Add the recipient's email

                // Content
                // Content
                $mail->isHTML(false);  // Set the email format to plain text
                $mail->Subject = 'Password Update Notification';
                $mail->Body = "Hello, your password has been successfully updated. Your new password is: {$new_password}. If you did not request this change, please contact us immediately. Regards, COLENS TECH";

                // Send the email
                if ($mail->send()) {
                    echo 'Password updated successfully. Notification email sent.';
                } else {
                    echo 'Error sending email: ' . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error updating password: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Passwords do not match.";
    }
}

$conn->close();
?>
<?php
// reset_password.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer if using Composer

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "colens"; // Update your database name here

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and is not expired in the members table
    $stmt = $conn->prepare("SELECT member_id, reset_token, token_expiry FROM members WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Token found, check if expired
        $stmt->bind_result($id, $dbToken, $expiry);
        $stmt->fetch();

        if (strtotime($expiry) > time()) {
            // Token is valid, proceed with password reset
            if (isset($_POST['newPassword'])) {
                $newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

                // Update the member's password and clear the reset token
                $updateStmt = $conn->prepare("UPDATE members SET password = ?, reset_token = NULL, token_expiry = NULL WHERE member_id = ?");
                $updateStmt->bind_param("si", $newPassword, $id);

                if ($updateStmt->execute()) {
                    echo '<div class="alert alert-success text-center">Your password has been reset successfully. You can now <a href="index.php">login</a>.</div>';
                } else {
                    echo '<div class="alert alert-danger text-center">Error updating password: ' . $updateStmt->error . '</div>';
                }
            }
        } else {
            echo '<div class="alert alert-danger text-center">The reset token has expired. Please request a new password reset.</div>';
        }
    } else {
        echo '<div class="alert alert-danger text-center">Invalid token. Please make sure the link is correct.</div>';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to external CSS file -->
</head>
<style>
    /* General styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7fc;
        color: #333;
    }

    .container {
        max-width: 500px;
        margin: 0 auto;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        font-size: 1.5rem;
        padding: 15px;
        text-transform: uppercase;
    }

    .card-body {
        padding: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
        margin-top: 5px;
    }

    button {
        padding: 12px;
        font-size: 1.1rem;
        width: 100%;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }

    button.btn-primary {
        background-color: #007bff;
        color: white;
    }

    button.btn-primary:hover {
        background-color: #0056b3;
    }

    .alert {
        margin-top: 10px;
        font-size: 1rem;
    }

    /* For smaller screens */
    @media (max-width: 600px) {
        .container {
            padding: 10px;
        }

        .card {
            margin-top: 10px;
        }
    }
</style>

<body class="bg-light">
    <div class="container">
        <div class="card mt-5">
            <div class="card-header bg-primary text-white text-center">
                Reset Your Password
            </div>
            <div class="card-body">
                <form action="reset_password.php?token=<?php echo $_GET['token']; ?>" method="POST"
                    id="resetPasswordForm">
                    <div class="form-group">
                        <label for="newPassword">Enter your new password:</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword"
                            placeholder="Enter new password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm your new password:</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                            placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </form>
                <p class="text-center mt-3"><a href="index.php">Back to Login</a></p>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#resetPasswordForm').submit(function (e) {
                e.preventDefault();

                var password = $('#newPassword').val();
                var confirmPassword = $('#confirmPassword').val();

                if (password !== confirmPassword) {
                    alert('Passwords do not match. Please try again.');
                } else {
                    this.submit();
                }
            });
        });
    </script>
</body>

</html>
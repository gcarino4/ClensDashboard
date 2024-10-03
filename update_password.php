<?php
// Connect to your database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "colens";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the username and new password from the AJAX request
$username = $_POST['username'];
$newPassword = $_POST['newPassword'];

// Hash the new password before saving (for security)
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password in the database
$sql = "UPDATE admin SET password = '$hashedPassword' WHERE username = '$username'";
if ($conn->query($sql) === TRUE) {
    echo "Password updated successfully";
} else {
    echo "Error updating password: " . $conn->error;
}

// Close connection
$conn->close();
?>
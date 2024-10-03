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

// Fetch the username from the AJAX request
$username = $_POST['username'];

// Query to check if the username exists
$sql = "SELECT * FROM admin WHERE username = '$username'";
$result = $conn->query($sql);

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Username exists
    echo "exists";
} else {
    // Username doesn't exist
    echo "not_exists";
}

// Close connection
$conn->close();
?>
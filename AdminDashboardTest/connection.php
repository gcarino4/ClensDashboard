<?php
$servername = "localhost";
$dbUsername = "root"; // Changed from $username to $dbUsername
$password = "root";
$dbname = "colens";

$conn = new mysqli($servername, $dbUsername, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
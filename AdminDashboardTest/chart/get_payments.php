<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost'; // your database host
$db = 'colens'; // your database name
$user = 'root'; // your database username
$pass = 'root'; // your database password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Modify the SQL query to group by type and sum the amounts
$query = "SELECT type, SUM(amount) AS total_amount FROM payments GROUP BY type";
$result = $conn->query($query);

$payments = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();

// Return the data as a JSON response
header('Content-Type: application/json');
echo json_encode($payments);
?>
<?php
// update_status.php
include 'connection.php'; // Make sure to include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $application_id = $conn->real_escape_string($_POST['application_id']);
    $status = $conn->real_escape_string($_POST['status']);

    // Prepare SQL statement
    $sql = "UPDATE health_insurance_applications SET status = '$status' WHERE application_id = '$application_id'";

    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }

    // Close connection
    $conn->close();
}
?>
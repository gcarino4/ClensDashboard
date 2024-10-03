<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    // Validate inputs
    if (!empty($application_id) && in_array($status, ['approved', 'rejected'])) {
        $sql = "UPDATE loan_applications SET status = ? WHERE application_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $status, $application_id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        echo 'invalid input';
    }
} else {
    echo 'invalid request';
}

$conn->close();
?>
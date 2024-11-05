<?php
include 'connection.php';

header('Content-Type: application/json');

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['transaction_number']) && isset($data['status'])) {
    $transaction_number = $data['transaction_number'];
    $status = $data['status'];

    // Prepare the SQL update statement
    $sql = "UPDATE health_payments SET status = ? WHERE transaction_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $status, $transaction_number);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
}

// Close the database connection
$conn->close();
?>
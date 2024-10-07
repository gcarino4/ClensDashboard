<?php
include 'connection.php';

// Start the session
session_start();

// Check if the member_id is set in the session
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Member ID is not set in the session.']);
    exit;
}

$member_id = $_SESSION['member_id'];

// Get the contribution ID and payment amount from POST request
if (!isset($_POST['contribution_id']) || !isset($_POST['payment_amount'])) {
    echo json_encode(['success' => false, 'message' => 'Contribution ID or payment amount is not set.']);
    exit;
}

$contribution_id = $_POST['contribution_id'];
$payment_amount = $_POST['payment_amount'];

// Get the Base64 image data
$image_data = isset($_POST['payment_image_base64']) ? $_POST['payment_image_base64'] : null;

// Start transaction
$conn->begin_transaction();

try {
    // Update the contribution amount based on the contribution ID
    $update_sql = "UPDATE contributions SET contribution_amount = contribution_amount + ? WHERE contribution_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ds", $payment_amount, $contribution_id); // Use "ds" for double and string
    if (!$stmt->execute()) {
        error_log("Error updating contributions: " . $stmt->error);
        throw new Exception("Database error updating contributions.");
    }

    // Fetch the new total contribution amount after the update
    $select_sql = "SELECT contribution_amount FROM contributions WHERE contribution_id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param("s", $contribution_id); // Use "s" for string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Contribution ID not found.");
    }

    // Get the updated contribution amount
    $row = $result->fetch_assoc();
    $new_contribution_amount = $row['contribution_amount'];

    // Record the payment in the contribution_payments table
    $insert_sql = "INSERT INTO contribution_payments (contribution_id, member_id, payment_amount, payment_image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssds", $contribution_id, $member_id, $payment_amount, $image_data); // Use "ssds" for string, string, double, string
    if (!$stmt->execute()) {
        error_log("Error inserting into contribution_payments: " . $stmt->error);
        throw new Exception("Database error inserting into contribution_payments.");
    }

    // Commit the transaction
    $conn->commit();

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Payment processed successfully.', 'contribution_id' => $contribution_id, 'new_amount' => $new_contribution_amount]);

} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error processing payment: ' . $e->getMessage()]);
}

// Close the connection
$conn->close();
?>
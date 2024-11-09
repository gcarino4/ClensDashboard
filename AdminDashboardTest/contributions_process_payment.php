<?php
include 'connection.php';

// Start the session
session_start();

// Get the member ID from POST request
if (!isset($_POST['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Member ID is not set.']);
    exit;
}

$member_id = $_POST['member_id'];

// Get the contribution ID and payment amount from POST request
if (!isset($_POST['contribution_id']) || !isset($_POST['payment_amount'])) {
    echo json_encode(['success' => false, 'message' => 'Contribution ID or payment amount is not set.']);
    exit;
}

$contribution_id = $_POST['contribution_id'];
$payment_amount = $_POST['payment_amount'];

// Validate payment amount
if ($payment_amount < 1500 || $payment_amount > 50000) {
    echo json_encode(['success' => false, 'message' => 'Payment amount must not be less than 1500 or more than 50000.']);
    exit;
}

// Get the Base64 image data
$image_data = isset($_POST['payment_image_base64']) ? $_POST['payment_image_base64'] : null;

$conn->begin_transaction();

try {
    // Insert payment record without checking for duplicates
    $insert_sql = "INSERT INTO contribution_payments (contribution_id, member_id, payment_amount, payment_image, payment_date) 
                   VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssds", $contribution_id, $member_id, $payment_amount, $image_data);
    if (!$stmt->execute()) {
        throw new Exception("Error inserting into contribution_payments.");
    }

    // Commit the transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Payment processed successfully.', 'contribution_id' => $contribution_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error processing payment: ' . $e->getMessage()]);
}

$conn->close();
?>
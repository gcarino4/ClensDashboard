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
if ($payment_amount < 1500 || $payment_amount > 5000) {
    echo json_encode(['success' => false, 'message' => 'Payment amount must not be less than 1500 or more than 5000.']);
    exit;
}

// Get the Base64 image data
$image_data = isset($_POST['payment_image_base64']) ? $_POST['payment_image_base64'] : null;

$conn->begin_transaction();

try {
    // Check if a payment record already exists for this member and contribution within the current month
    $check_sql = "SELECT * FROM contribution_payments 
                  WHERE member_id = ? 
                  AND contribution_id = ? 
                  AND MONTH(payment_date) = MONTH(CURRENT_DATE())
                  AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $member_id, $contribution_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        throw new Exception("A payment has already been made for this contribution this month.");
    }

    // Update the contribution amount based on the contribution ID
    $update_sql = "UPDATE contributions SET contribution_amount = contribution_amount + ? WHERE contribution_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ds", $payment_amount, $contribution_id);
    if (!$stmt->execute()) {
        throw new Exception("Database error updating contributions.");
    }

    // Fetch the new total contribution amount after the update
    $select_sql = "SELECT contribution_amount FROM contributions WHERE contribution_id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param("s", $contribution_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Contribution ID not found.");
    }

    $row = $result->fetch_assoc();
    $new_contribution_amount = $row['contribution_amount'];

    // Insert payment record
    $insert_sql = "INSERT INTO contribution_payments (contribution_id, member_id, payment_amount, payment_image, payment_date) 
                   VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssds", $contribution_id, $member_id, $payment_amount, $image_data);
    if (!$stmt->execute()) {
        throw new Exception("Error inserting into contribution_payments.");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Payment processed successfully.', 'contribution_id' => $contribution_id, 'new_amount' => $new_contribution_amount]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error processing payment: ' . $e->getMessage()]);
}

$conn->close();
?>
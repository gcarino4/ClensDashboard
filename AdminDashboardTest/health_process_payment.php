<?php
namespace health_process_payment;

include 'connection.php';

// Ensure session is started and member_id is set
session_start();
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Member ID is not set in the session.']);
    exit;
}

$session_member_id = $_SESSION['member_id'];

// Get payment data from POST request
$application_id = $_POST['application_id'] ?? null;
$payment_amount = $_POST['payment_amount'] ?? null;
$payment_date = $_POST['payment_date'] ?? null;
$payment_notes = $_POST['payment_notes'] ?? null;

// Fetch the payment_due from the approved_health_insurance table
$sql_due = "SELECT payment_due FROM approved_health_insurance WHERE application_id = ? AND member_id = ?";
$stmt_due = $conn->prepare($sql_due);
$stmt_due->bind_param("ss", $application_id, $session_member_id);
$stmt_due->execute();
$result_due = $stmt_due->get_result();

if ($result_due->num_rows > 0) {
    $due_data = $result_due->fetch_assoc();
    $payment_due = floatval($due_data['payment_due']);
} else {
    echo json_encode(['success' => false, 'message' => 'Payment due not found for this application.']);
    exit;
}

// Validate payment amount
if (abs(floatval($payment_amount) - $payment_due) > 1) { // Adjust the tolerance as needed
    echo json_encode(['success' => false, 'message' => 'We only accept whole number payments']);
    exit;
}

// Generate a unique transaction number using uniqid
$transaction_number = uniqid('txn_', true);  // Example: txn_651a1bd8bdf9c1.17650468

// Prepare the SQL statement to insert payment details including the transaction number
$sql = "INSERT INTO health_payments (application_id, member_id, payment_amount, payment_date, notes, payment_due, transaction_number) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

// Bind parameters to the statement, including the transaction number
$stmt->bind_param("sssssss", $application_id, $session_member_id, $payment_amount, $payment_date, $payment_notes, $payment_due, $transaction_number);

// Execute the statement and check for success
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'transaction_number' => $transaction_number]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error executing statement: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
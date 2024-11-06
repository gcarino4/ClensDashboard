<?php

include 'connection.php';

session_start();
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Member ID is not set in the session.']);
    exit;
}

$session_member_id = $_SESSION['member_id'];

$application_id = $_POST['application_id'] ?? null;
$payment_amount = $_POST['payment_amount'] ?? null;
$payment_date = $_POST['payment_date'] ?? null;
$payment_notes = $_POST['payment_notes'] ?? null;

// Handle image upload and convert to Base64
$payment_image = null; // Initialize variable for the payment image

if (isset($_FILES['payment_image']) && $_FILES['payment_image']['error'] === UPLOAD_ERR_OK) {
    $image = $_FILES['payment_image'];
    $imageData = file_get_contents($image['tmp_name']); // Get the file content
    $payment_image = 'data:' . $image['type'] . ';base64,' . base64_encode($imageData); // Convert to Base64
} else {
    echo json_encode(['success' => false, 'message' => 'Payment image is required.']);
    exit;
}

// Fetch the payment_due and next_payment_due_date from the approved_health_insurance table
$sql_due = "SELECT payment_due, next_payment_due_date FROM approved_health_insurance WHERE application_id = ? AND member_id = ?";
$stmt_due = $conn->prepare($sql_due);
$stmt_due->bind_param("ss", $application_id, $session_member_id);
$stmt_due->execute();
$result_due = $stmt_due->get_result();

if ($result_due->num_rows > 0) {
    $due_data = $result_due->fetch_assoc();
    $payment_due = floatval($due_data['payment_due']);
    $next_payment_due_date = $due_data['next_payment_due_date'];
} else {
    echo json_encode(['success' => false, 'message' => 'Payment due or next payment due date not found for this application.']);
    exit;
}

// Check if the current date is greater than the next payment due date
$current_date = new DateTime(); // Get current date
$next_payment_due_date_obj = new DateTime($next_payment_due_date); // Convert next payment due date from string

// Late fee calculation if overdue
$late_fee = 0;
$late_portion = 0; // Initialize late portion
if ($current_date > $next_payment_due_date_obj) {
    // Apply 1% late fee
    $late_fee = $payment_due * 0.01;
    $late_portion = $late_fee; // Store the late portion value
    $payment_due += $late_fee; // Add the late fee to the due amount
}

// Fetch the member_name from the members table
$sql_member = "SELECT name FROM members WHERE member_id = ?";
$stmt_member = $conn->prepare($sql_member);
$stmt_member->bind_param("s", $session_member_id);
$stmt_member->execute();
$result_member = $stmt_member->get_result();

if ($result_member->num_rows > 0) {
    $member_data = $result_member->fetch_assoc();
    $member_name = $member_data['name'];
} else {
    echo json_encode(['success' => false, 'message' => 'Member name not found.']);
    exit;
}

// Validate payment amount
if (abs(floatval($payment_amount) - $payment_due) > 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment amount.']);
    exit;
}

// Generate a unique transaction number
$transaction_number = uniqid('txn_', true);

// Insert payment details into the database including the Base64 image
$sql = "INSERT INTO health_payments (application_id, member_id, member_name, payment_amount, payment_date, notes, payment_due, transaction_number, payment_image, late_portion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ssssssssss", $application_id, $session_member_id, $member_name, $payment_amount, $payment_date, $payment_notes, $payment_due, $transaction_number, $payment_image, $late_portion);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'transaction_number' => $transaction_number, 'late_portion' => $late_portion]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error executing statement: ' . $stmt->error]);
}

$stmt->close();
$conn->close();

?>
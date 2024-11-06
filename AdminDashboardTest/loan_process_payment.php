<?php
session_start();
include 'connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$application_id = $data['application_id'];
$member_id = $data['member_id'];
$payment_amount = $data['payment_amount'];
$payment_image_base64 = $data['payment_image_base64'] ?? null; // Get Base64 image

if (!$payment_image_base64) {
    echo json_encode(['success' => false, 'message' => 'No payment image uploaded.']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Fetch the member's name from the database
    $member_name_sql = "SELECT name FROM members WHERE member_id = ?";
    $member_name_stmt = $conn->prepare($member_name_sql);
    $member_name_stmt->bind_param("s", $member_id);
    $member_name_stmt->execute();
    $member_name_result = $member_name_stmt->get_result();
    if ($member_name_result->num_rows === 0) {
        throw new Exception('Member not found.');
    }
    $member_data = $member_name_result->fetch_assoc();
    $member_name = $member_data['name'];

    // Fetch loan details from approved_loans, including loan_amount as the initial updated_loan_amount
    $loan_sql = "SELECT loan_amount, loan_term, payment_plan, next_payment_due_date FROM approved_loans WHERE application_id = ? AND member_id = ?";
    $loan_stmt = $conn->prepare($loan_sql);
    $loan_stmt->bind_param("ss", $application_id, $member_id);
    $loan_stmt->execute();
    $loan_result = $loan_stmt->get_result();
    if ($loan_result->num_rows === 0) {
        throw new Exception('Approved loan not found.');
    }
    $loan_data = $loan_result->fetch_assoc();
    $updated_loan_amount = $loan_data['loan_amount'];
    $loan_term = $loan_data['loan_term'];
    $payment_plan = $loan_data['payment_plan'];
    $next_payment_due_date = new DateTime($loan_data['next_payment_due_date']);
    $current_date = new DateTime();

    // Calculate minimum payment based on the updated_loan_amount, loan term, and payment plan
    $min_payment_amount = 0;
    switch ($payment_plan) {
        case 'monthly':
            $min_payment_amount = $updated_loan_amount / ($loan_term * 12);
            break;
        case 'quarterly':
            $min_payment_amount = $updated_loan_amount / ($loan_term * 4);
            break;
        case 'annually':
            $min_payment_amount = $updated_loan_amount / $loan_term;
            break;
        default:
            throw new Exception('Invalid payment plan specified.');
    }

    // Apply a 1% penalty if payment is overdue
    if ($current_date > $next_payment_due_date) {
        $min_payment_amount += $min_payment_amount * 0.01;
    }

    // Validate the payment amount (should be at least the minimum payment)
    if ($payment_amount < $min_payment_amount) {
        throw new Exception('Payment amount must not be less than ' . number_format($min_payment_amount, 2));
    }

    // Insert payment record into loan_payments table
    $transaction_number = uniqid('txn_', true);
    $insert_sql = "INSERT INTO loan_payments 
        (application_id, member_id, member_name, loan_term, payment_plan, payment_amount, transaction_number, updated_loan_amount, payment_image) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssssssss", $application_id, $member_id, $member_name, $loan_term, $payment_plan, $payment_amount, $transaction_number, $updated_loan_amount, $payment_image_base64);
    if (!$insert_stmt->execute()) {
        throw new Exception('Failed to record the payment transaction: ' . $insert_stmt->error);
    }

    // Commit the transaction
    $conn->commit();
    echo json_encode(['success' => true, 'transaction_number' => $transaction_number]);

} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
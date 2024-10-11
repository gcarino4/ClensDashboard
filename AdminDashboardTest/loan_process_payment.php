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

    // Fetch the original loan amount from loan_applications
    $loan_app_sql = "SELECT loan_amount FROM loan_applications WHERE application_id = ? AND member_id = ?";
    $loan_app_stmt = $conn->prepare($loan_app_sql);
    $loan_app_stmt->bind_param("ss", $application_id, $member_id);
    $loan_app_stmt->execute();
    $loan_app_result = $loan_app_stmt->get_result();
    if ($loan_app_result->num_rows === 0) {
        throw new Exception('Loan application not found.');
    }
    $loan_app_data = $loan_app_result->fetch_assoc();
    $original_loan_amount = $loan_app_data['loan_amount'];

    // Fetch loan details from approved_loans
    $select_sql = "SELECT loan_term, payment_plan, loan_amount, next_payment_due_date FROM approved_loans WHERE application_id = ? AND member_id = ?";
    $select_stmt = $conn->prepare($select_sql);
    $select_stmt->bind_param("ss", $application_id, $member_id);
    $select_stmt->execute();
    $select_result = $select_stmt->get_result();
    if ($select_result->num_rows === 0) {
        throw new Exception('Approved loan not found.');
    }
    $loan_data = $select_result->fetch_assoc();
    $loan_term = $loan_data['loan_term'];
    $payment_plan = $loan_data['payment_plan'];
    $remaining_loan_amount = $loan_data['loan_amount'];  // Dynamic amount
    $next_payment_due_date = new DateTime($loan_data['next_payment_due_date']);
    $current_date = new DateTime();

    // Calculate minimum payment based on the original loan amount, loan term, and payment plan
    $min_payment_amount = 0;

    switch ($payment_plan) {
        case 'monthly':
            $min_payment_amount = ($original_loan_amount / ($loan_term * 12));
            break;
        case 'quarterly':
            $min_payment_amount = ($original_loan_amount / ($loan_term * 4));
            break;
        case 'annually':
            $min_payment_amount = ($original_loan_amount / $loan_term);
            break;
        default:
            throw new Exception('Invalid payment plan specified.');
    }

    // Check if payment is overdue
    if ($current_date > $next_payment_due_date) {
        // Apply a 1% penalty to the minimum payment amount if overdue
        $min_payment_amount += $min_payment_amount * 0.01;
    }


    // Check if the payment amount is valid (should be at least the minimum payment)
    if ($payment_amount < $min_payment_amount) {
        throw new Exception('Payment amount must not be less than ' . number_format($min_payment_amount, 2));
    }

    // Update the remaining loan amount in the approved_loans table
    $updated_loan_amount = $remaining_loan_amount - $payment_amount;
    $update_sql = "UPDATE approved_loans SET loan_amount = ? WHERE application_id = ? AND member_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("dss", $updated_loan_amount, $application_id, $member_id);
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update loan amount in approved_loans: ' . $update_stmt->error);
    }

    // Calculate the new next payment due date based on the payment plan
    switch ($payment_plan) {
        case 'monthly':
            $next_payment_due_date->modify('+1 month');
            break;
        case 'quarterly':
            $next_payment_due_date->modify('+3 months');
            break;
        case 'annually':
            $next_payment_due_date->modify('+1 year');
            break;
    }

    // Update the next payment due date in the approved_loans table
    $new_next_due_date = $next_payment_due_date->format('Y-m-d');
    $update_due_date_sql = "UPDATE approved_loans SET next_payment_due_date = ? WHERE application_id = ? AND member_id = ?";
    $update_due_date_stmt = $conn->prepare($update_due_date_sql);
    $update_due_date_stmt->bind_param("sss", $new_next_due_date, $application_id, $member_id);
    if (!$update_due_date_stmt->execute()) {
        throw new Exception('Failed to update next payment due date: ' . $update_due_date_stmt->error);
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
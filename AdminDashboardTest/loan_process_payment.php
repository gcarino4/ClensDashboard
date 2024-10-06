<?php
session_start();
include 'connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$session_member_id = $_SESSION['member_id'];

$data = json_decode(file_get_contents('php://input'), true);
$application_id = $data['application_id'];
$member_id = $data['member_id'];
$payment_amount = $data['payment_amount'];

// Start transaction
$conn->begin_transaction();

try {
    // Fetch member_name from the members table
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

    // Select loan data (updated_loan_amount is the original loan amount before any deductions)
    $select_sql = "SELECT loan_term, payment_plan, loan_amount FROM approved_loans WHERE application_id = ? AND member_id = ?";
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
    $original_loan_amount = $loan_data['loan_amount']; // This is the current amount before the payment

    // Calculate minimum payment amount
    $min_payment_amount = 0;

    switch ($payment_plan) {
        case 'monthly':
            $min_payment_amount = ($original_loan_amount / ($loan_term * 12)); // Total payments in months
            break;
        case 'quarterly':
            $min_payment_amount = ($original_loan_amount / ($loan_term * 4)); // Total payments in quarters
            break;
        case 'annually':
            $min_payment_amount = ($original_loan_amount / $loan_term); // Total payments in years
            break;
        default:
            throw new Exception('Invalid payment plan specified.');
    }

    // Check if the payment amount is less than the minimum required amount
    if ($payment_amount < $min_payment_amount) {
        throw new Exception('Payment amount must not be less than ' . number_format($min_payment_amount, 2));
    }

    // Calculate the new loan balance after payment
    $updated_loan_amount = $original_loan_amount - $payment_amount;

    // Update loan amount in approved_loans table
    $update_sql = "UPDATE approved_loans SET loan_amount = ? WHERE application_id = ? AND member_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("dss", $updated_loan_amount, $application_id, $member_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update loan amount in approved_loans: ' . $stmt->error);
    }

    // Generate transaction number
    $transaction_number = uniqid('txn_', true);

    // Insert payment transaction including the updated loan amount after payment
    $insert_sql = "INSERT INTO loan_payments (application_id, member_id, member_name, loan_term, payment_plan, payment_amount, transaction_number, updated_loan_amount) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssssssss", $application_id, $member_id, $member_name, $loan_term, $payment_plan, $payment_amount, $transaction_number, $updated_loan_amount);

    if (!$insert_stmt->execute()) {
        throw new Exception('Failed to record the payment transaction: ' . $insert_stmt->error);
    }


    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'transaction_number' => $transaction_number]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($insert_stmt)) {
        $insert_stmt->close();
    }
    if (isset($select_stmt)) {
        $select_stmt->close();
    }
    if (isset($member_name_stmt)) {
        $member_name_stmt->close();
    }
    $conn->close();
}

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

$conn->begin_transaction();

try {
    // Select loan data
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
    $updated_loan_amount = $loan_data['loan_amount'];

    // Calculate minimum payment amount
    $min_payment_amount = 0;

    switch ($payment_plan) {
        case 'monthly':
            $min_payment_amount = ($updated_loan_amount / ($loan_term * 12)); // Total payments in months
            break;
        case 'quarterly':
            $min_payment_amount = ($updated_loan_amount / ($loan_term * 4)); // Total payments in quarters
            break;
        case 'annually':
            $min_payment_amount = ($updated_loan_amount / $loan_term); // Total payments in years
            break;
        default:
            throw new Exception('Invalid payment plan specified.');
    }

    // Check if the payment amount is less than the minimum required amount
    if ($payment_amount < $min_payment_amount) {
        throw new Exception('Payment amount must not be less than ' . number_format($min_payment_amount, 2));
    }

    // Update loan amount
    $update_sql = "UPDATE approved_loans SET loan_amount = loan_amount - ? WHERE application_id = ? AND member_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iss", $payment_amount, $application_id, $member_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update loan amount in approved_loans: ' . $stmt->error);
    }

    // Generate transaction number
    $transaction_number = uniqid('txn_', true);

    // Insert payment transaction
    $insert_sql = "INSERT INTO loan_payments (application_id, member_id, loan_term, payment_plan, payment_amount, transaction_number, updated_loan_amount) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssssss", $application_id, $member_id, $loan_term, $payment_plan, $payment_amount, $transaction_number, $updated_loan_amount);

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
    $conn->close();
}

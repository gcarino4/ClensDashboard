<?php
session_start();
include 'connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$application_id = $data['application_id'];
$member_id = $data['member_id'];

// Fetch loan data
$select_sql = "SELECT loan_term, payment_plan, loan_amount, next_payment_due_date FROM approved_loans WHERE application_id = ? AND member_id = ?";
$select_stmt = $conn->prepare($select_sql);
$select_stmt->bind_param("ss", $application_id, $member_id);
$select_stmt->execute();
$select_result = $select_stmt->get_result();
if ($select_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Approved loan not found.']);
    exit;
}
$loan_data = $select_result->fetch_assoc();
$loan_term = $loan_data['loan_term'];
$payment_plan = $loan_data['payment_plan'];
$original_loan_amount = $loan_data['loan_amount'];
$next_payment_due_date = $loan_data['next_payment_due_date'];

// Calculate minimum payment based on the loan term and payment plan
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
        echo json_encode(['success' => false, 'message' => 'Invalid payment plan specified.']);
        exit;
}

// Check if the payment is overdue
if (strtotime($next_payment_due_date) < time()) {
    echo json_encode(['success' => false, 'message' => 'Payment is overdue.']);
    exit;
}

echo json_encode(['success' => true, 'min_payment_amount' => number_format($min_payment_amount, 2)]);
$conn->close();

<?php

include 'connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$transaction_number = $data['transaction_number'];
$new_status = $data['status'];

$conn->begin_transaction();

try {
    // Fetch payment record
    $sql = "SELECT payment_amount, updated_loan_amount, status, application_id, member_id FROM loan_payments WHERE transaction_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $transaction_number);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();

    if (!$payment) {
        throw new Exception("Payment record not found.");
    }

    if ($payment['status'] === 'pending' && $new_status === 'approved') {
        // Calculate the current balance
        $current_balance = $payment['updated_loan_amount'] - $payment['payment_amount'];

        // Update payment record with new status and updated balance
        $update_sql = "UPDATE loan_payments SET status = ?, updated_loan_amount = ? WHERE transaction_number = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sds", $new_status, $current_balance, $transaction_number);
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update payment status.");
        }

        // Update loan balance in approved_loans
        $update_loan_sql = "UPDATE approved_loans SET loan_amount = ? WHERE application_id = ? AND member_id = ?";
        $update_loan_stmt = $conn->prepare($update_loan_sql);
        $update_loan_stmt->bind_param("dss", $current_balance, $payment['application_id'], $payment['member_id']);
        if (!$update_loan_stmt->execute()) {
            throw new Exception("Failed to update loan amount in approved_loans.");
        }

        // Fetch current next payment due date and payment plan
        $fetch_loan_sql = "SELECT next_payment_due_date, payment_plan FROM approved_loans WHERE application_id = ? AND member_id = ?";
        $fetch_loan_stmt = $conn->prepare($fetch_loan_sql);
        $fetch_loan_stmt->bind_param("ss", $payment['application_id'], $payment['member_id']);
        $fetch_loan_stmt->execute();
        $loan_data = $fetch_loan_stmt->get_result()->fetch_assoc();

        if (!$loan_data) {
            throw new Exception("Loan record not found in approved_loans.");
        }

        // Calculate the new next payment due date based on the payment plan
        $next_payment_due_date = new DateTime($loan_data['next_payment_due_date']);

        switch ($loan_data['payment_plan']) {
            case 'monthly':
                $next_payment_due_date->modify('+1 month');
                break;
            case 'quarterly':
                $next_payment_due_date->modify('+3 months');
                break;
            case 'annually':
                $next_payment_due_date->modify('+1 year');
                break;
            default:
                throw new Exception("Invalid payment plan.");
        }

        // Format the new next due date to 'Y-m-d' format
        $new_next_due_date = $next_payment_due_date->format('Y-m-d');

        // Update the next payment due date in approved_loans
        $update_next_due_sql = "UPDATE approved_loans SET next_payment_due_date = ? WHERE application_id = ? AND member_id = ?";
        $update_next_due_stmt = $conn->prepare($update_next_due_sql);
        $update_next_due_stmt->bind_param("sss", $new_next_due_date, $payment['application_id'], $payment['member_id']);
        if (!$update_next_due_stmt->execute()) {
            throw new Exception("Failed to update next payment due date in approved_loans.");
        }

    } else {
        $update_sql = "UPDATE loan_payments SET status = ? WHERE transaction_number = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_status, $transaction_number);
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update payment status.");
        }
    }

    // Commit the transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);

} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    // Close prepared statements and connection
    if (isset($stmt))
        $stmt->close();
    if (isset($update_stmt))
        $update_stmt->close();
    if (isset($update_loan_stmt))
        $update_loan_stmt->close();
    if (isset($fetch_loan_stmt))
        $fetch_loan_stmt->close();
    if (isset($update_next_due_stmt))
        $update_next_due_stmt->close();
    $conn->close();
}
?>
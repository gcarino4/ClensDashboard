<?php
namespace Update_loan_status;

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

    if (!$payment)
        throw new Exception("Payment record not found.");

    if ($payment['status'] === 'pending' && $new_status === 'approved') {
        $current_balance = $payment['updated_loan_amount'] - $payment['payment_amount'];

        // Update payment record with new status and updated balance
        $update_sql = "UPDATE loan_payments SET status = ?, updated_loan_amount = ? WHERE transaction_number = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sds", $new_status, $current_balance, $transaction_number);
        if (!$update_stmt->execute())
            throw new Exception("Failed to update payment status.");

        // Update loan balance in approved_loans
        $update_loan_sql = "UPDATE approved_loans SET loan_amount = ? WHERE application_id = ? AND member_id = ?";
        $update_loan_stmt = $conn->prepare($update_loan_sql);
        $update_loan_stmt->bind_param("dss", $current_balance, $payment['application_id'], $payment['member_id']);
        if (!$update_loan_stmt->execute())
            throw new Exception("Failed to update loan amount in approved_loans.");
    } else {
        $update_sql = "UPDATE loan_payments SET status = ? WHERE transaction_number = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_status, $transaction_number);
        if (!$update_stmt->execute())
            throw new Exception("Failed to update payment status.");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
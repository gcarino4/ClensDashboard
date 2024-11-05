<?php
include 'connection.php';

// Get the JSON input from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$contribution_id = $data['contribution_id'];
$payment_amount = $data['payment_amount'];
$status = $data['status']; // Retrieve the status ('approved' or 'rejected')

$conn->begin_transaction();

try {
    if ($status === 'approved') {
        // Update the status of the payment to 'approved'
        $update_payment_sql = "UPDATE contribution_payments SET status = 'approved' WHERE contribution_id = ?";
        $stmt = $conn->prepare($update_payment_sql);
        $stmt->bind_param("s", $contribution_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update payment status to approved.");
        }

        // Update the contribution amount in the contributions table
        $update_contribution_sql = "UPDATE contributions SET contribution_amount = contribution_amount + ? WHERE contribution_id = ?";
        $stmt = $conn->prepare($update_contribution_sql);
        $stmt->bind_param("ds", $payment_amount, $contribution_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update contribution amount.");
        }

        $message = 'Payment approved and contribution updated.';
    } elseif ($status === 'rejected') {
        // Update the status of the payment to 'rejected'
        $update_payment_sql = "UPDATE contribution_payments SET status = 'rejected' WHERE contribution_id = ?";
        $stmt = $conn->prepare($update_payment_sql);
        $stmt->bind_param("s", $contribution_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update payment status to rejected.");
        }

        $message = 'Payment rejected successfully.';
    } else {
        throw new Exception("Invalid status provided.");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => $message]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
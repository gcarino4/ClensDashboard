<?php
include 'connection.php';

header('Content-Type: application/json');

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['transaction_number']) && isset($data['status'])) {
    $transaction_number = $data['transaction_number'];
    $status = $data['status'];

    // Start transaction to ensure both updates happen together
    $conn->begin_transaction();

    try {
        // Prepare the SQL update statement for updating the status in health_payments
        $sql = "UPDATE health_payments SET status = ? WHERE transaction_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $status, $transaction_number);

        if (!$stmt->execute()) {
            throw new Exception('Failed to update status.');
        }

        // Now, get the corresponding application_id from health_payments to use in approved_health_insurance
        $sql_transaction = "SELECT application_id FROM health_payments WHERE transaction_number = ?";
        $stmt_transaction = $conn->prepare($sql_transaction);
        $stmt_transaction->bind_param("s", $transaction_number);
        $stmt_transaction->execute();
        $result_transaction = $stmt_transaction->get_result();

        if ($result_transaction->num_rows > 0) {
            $row_transaction = $result_transaction->fetch_assoc();
            $application_id = $row_transaction['application_id'];

            // Now, get the current next_payment_due_date and payment_plan from approved_health_insurance
            $sql_due_date = "SELECT next_payment_due_date, payment_plan FROM approved_health_insurance WHERE application_id = ?";
            $stmt_due_date = $conn->prepare($sql_due_date);
            $stmt_due_date->bind_param("s", $application_id);
            $stmt_due_date->execute();
            $result_due_date = $stmt_due_date->get_result();

            if ($result_due_date->num_rows > 0) {
                $row_due_date = $result_due_date->fetch_assoc();
                $current_due_date = $row_due_date['next_payment_due_date'];
                $payment_plan = $row_due_date['payment_plan'];

                // Calculate the new next_payment_due_date based on the payment plan
                $next_payment_due_date = calculateNextDueDate($current_due_date, $payment_plan);

                // Update the next_payment_due_date in approved_health_insurance
                $sql_update_due_date = "UPDATE approved_health_insurance SET next_payment_due_date = ? WHERE application_id = ?";
                $stmt_update_due_date = $conn->prepare($sql_update_due_date);
                $stmt_update_due_date->bind_param("ss", $next_payment_due_date, $application_id);

                if (!$stmt_update_due_date->execute()) {
                    throw new Exception('Failed to update next_payment_due_date.');
                }

                // Commit transaction
                $conn->commit();

                // Respond with success and the updated next payment due date
                echo json_encode(['success' => true, 'next_payment_due_date' => $next_payment_due_date]);
            } else {
                throw new Exception('No payment information found for the application ID.');
            }
        } else {
            throw new Exception('No transaction found for the given transaction number.');
        }

        // Close statements
        $stmt_transaction->close();
        $stmt_due_date->close();
        $stmt_update_due_date->close();
        $stmt->close();

    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
}

// Close the database connection
$conn->close();

// Function to calculate the next payment due date based on the payment plan
function calculateNextDueDate($current_due_date, $payment_plan)
{
    $date = new DateTime($current_due_date);

    switch ($payment_plan) {
        case 'Monthly':
            $date->modify('+1 month');
            break;
        case 'Quarterly':
            $date->modify('+3 months');
            break;
        case 'Annually':
            $date->modify('+1 year');
            break;
        default:
            // If no specific payment plan, return the current date
            $date->modify('+1 month');
            break;
    }

    return $date->format('Y-m-d'); // Return the next due date in 'YYYY-MM-DD' format
}
?>
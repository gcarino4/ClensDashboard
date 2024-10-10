<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    // Fetch the loan's payment plan and approval date
    $sql = "SELECT payment_plan, approval_date FROM loan_applications WHERE application_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $application_id);
    $stmt->execute();
    $stmt->bind_result($payment_plan, $approval_date);
    $stmt->fetch();
    $stmt->close();

    // Check if the loan is being approved
    if ($status === 'approved') {
        // Set the approval date to the current date if not already set
        if (empty($approval_date)) {
            $approval_date = date('Y-m-d');
        }

        // Calculate the due date based on the payment plan
        switch ($payment_plan) {
            case 'monthly':
                $due_date = date('Y-m-d', strtotime('+1 month', strtotime($approval_date)));
                break;
            case 'quarterly':
                $due_date = date('Y-m-d', strtotime('+3 months', strtotime($approval_date)));
                break;
            case 'annually':
                $due_date = date('Y-m-d', strtotime('+1 year', strtotime($approval_date)));
                break;
            default:
                $due_date = $approval_date; // Default fallback if no plan
        }

        // Update the loan's status to 'approved', set the approval_date and initial due_date
        $update_sql = "UPDATE loan_applications 
                       SET status = ?, approval_date = ?, due_date = ? 
                       WHERE application_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('ssss', $status, $approval_date, $due_date, $application_id);
        if ($update_stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $update_stmt->close();
    } elseif ($status === 'rejected') {
        // If rejecting, just update the status
        $update_sql = "UPDATE loan_applications SET status = ? WHERE application_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('ss', $status, $application_id);
        if ($update_stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $update_stmt->close();
    }
}

$conn->close();
?>
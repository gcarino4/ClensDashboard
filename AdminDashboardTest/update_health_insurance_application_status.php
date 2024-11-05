<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    if ($status === 'approved') {
        $approval_date = date('Y-m-d H:i:s');

        $sql = "SELECT payment_plan FROM health_insurance_applications WHERE application_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $application_id);
        $stmt->execute();
        $stmt->bind_result($payment_plan);
        $stmt->fetch();
        $stmt->close();

        switch ($payment_plan) {
            case 'monthly':
                $due_date = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($approval_date)));
                break;
            case 'quarterly':
                $due_date = date('Y-m-d H:i:s', strtotime('+3 months', strtotime($approval_date)));
                break;
            case 'annually':
                $due_date = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($approval_date)));
                break;
            default:
                $due_date = $approval_date;
        }

        $update_sql = "UPDATE health_insurance_applications 
                       SET status = ?, approved_date = ?, due_date = ? 
                       WHERE application_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('ssss', $status, $approval_date, $due_date, $application_id);
        echo $update_stmt->execute() ? 'success' : 'error';
        $update_stmt->close();

    } else {
        $sql = "UPDATE health_insurance_applications SET status = ? WHERE application_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $status, $application_id);
        echo $stmt->execute() ? 'success' : 'error';
        $stmt->close();
    }

    $conn->close();
}


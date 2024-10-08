<?php

include 'connection.php';

// Start the session to access session variables
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $member_id = $conn->real_escape_string($_POST['member_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $annual_income = (float) $_POST['annual_income'];
    $loan_amount = (float) $_POST['loan_amount'];
    $loan_term = (int) $_POST['loan_term']; // Make sure the name matches here
    $loan_purpose = $conn->real_escape_string($_POST['loan_purpose']);
    $bank_info = isset($_POST['bank_info']) ? $conn->real_escape_string($_POST['bank_info']) : null;
    $employment_status = $conn->real_escape_string($_POST['employment_status']);
    $collateral_image = isset($_FILES['collateral_image']['tmp_name']) ? $_FILES['collateral_image']['tmp_name'] : null; // Handle upload
    $payment_plan = $conn->real_escape_string($_POST['payment_plan']);  // Collect the selected payment plan

    // Generate a 10-digit auto-generated application ID with prefix 'loan'
    $random_number = mt_rand(100000000, 999999999); // Generate a 9-digit random number
    $application_id = 'loan' . $random_number; // Concatenate 'loan' with the random number

    // Default status for a new application
    $status = 'Pending';

    // Determine the interest rate based on loan term
    switch ($loan_term) {
        case 1:
            $interest_rate = 2;  // 2% interest rate for 1 year term
            break;
        case 3:
            $interest_rate = 6;  // 6% interest rate for 3 years term
            break;
        case 5:
            $interest_rate = 10; // 10% interest rate for 5 years term
            break;
        default:
            $interest_rate = 0;  // Default to 0 if the term doesn't match
            break;
    }

    // Calculate the final loan amount based on the interest rate
    $loan_amount = $loan_amount + ($loan_amount * ($interest_rate / 100));

    // Handling file upload and converting to Base64
    function base64_encode_image($file)
    {
        $imageData = file_get_contents($file);
        return base64_encode($imageData);
    }

    // Check if files are uploaded and convert them to Base64
    $collateral_image = $collateral_image ? base64_encode_image($collateral_image) : null;
    $supporting_document_1 = isset($_FILES['supporting_document_1']['tmp_name']) ? base64_encode_image($_FILES['supporting_document_1']['tmp_name']) : null;
    $supporting_document_2 = isset($_FILES['supporting_document_2']['tmp_name']) ? base64_encode_image($_FILES['supporting_document_2']['tmp_name']) : null;

    // Check contributions for the current member
    $contribution_query = "SELECT contribution_amount FROM contributions WHERE member_id = '" . $_SESSION['member_id'] . "'";
    $contribution_result = $conn->query($contribution_query);
    $contribution_amount = 0;

    if ($contribution_result && $contribution_result->num_rows > 0) {
        $row = $contribution_result->fetch_assoc();
        $contribution_amount = (float) $row['contribution_amount'];
    }

    // Set loan limits based on contribution amount
    if ($contribution_amount > 10000 && $contribution_amount < 20000) {
        if ($loan_amount >= 30000) {
            echo "<script>alert('Your loan amount must be less than 30,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 20000 && $contribution_amount < 30000) {
        if ($loan_amount >= 50000) {
            echo "<script>alert('Your loan amount must be less than 50,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 30000 && $contribution_amount < 40000) {
        if ($loan_amount >= 70000) {
            echo "<script>alert('Your loan amount must be less than 70,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 40000 && $contribution_amount < 50000) {
        if ($loan_amount >= 100000) {
            echo "<script>alert('Your loan amount must be less than 100,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 50000 && $contribution_amount < 60000) {
        if ($loan_amount >= 200000) {
            echo "<script>alert('Your loan amount must be less than 200,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 60000 && $contribution_amount < 70000) {
        if ($loan_amount >= 300000) {
            echo "<script>alert('Your loan amount must be less than 300,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 70000 && $contribution_amount < 80000) {
        if ($loan_amount >= 400000) {
            echo "<script>alert('Your loan amount must be less than 400,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 80000 && $contribution_amount < 90000) {
        if ($loan_amount >= 500000) {
            echo "<script>alert('Your loan amount must be less than 500,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 90000) {
        if ($loan_amount >= 1000000) {
            echo "<script>alert('Your loan amount must be less than 1,000,000.'); window.history.back();</script>";
            exit;
        }
    }

    // Prepare SQL statement
    // Corrected SQL statement
    $sql = "INSERT INTO loan_applications (application_id, member_id, name, email, phone_number, address, annual_income, loan_amount, bank_info, loan_term, interest_rate, loan_purpose, employment_status, collateral_image, payment_plan, status, supporting_document_1, supporting_document_2)
    VALUES ('$application_id', '$member_id', '$name', '$email', '$phone_number', '$address', $annual_income, $loan_amount, '$bank_info', $loan_term, $interest_rate, '$loan_purpose', '$employment_status', '$collateral_image', '$payment_plan', '$status', '$supporting_document_1', '$supporting_document_2')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Application submitted successfully.'); window.location = 'index_member.php';</script>";
        exit; // Ensure no further code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
<?php
session_start();
require 'connection.php'; // Include your DB connection script

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $member_id = $_POST['member_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $savings_amount = $_POST['savings_amount'];
    $interest_rate = $_POST['interest_rate'];
    $savings_term = $_POST['savings_term'];
    $savings_purpose = $_POST['savings_purpose'];
    $savings_account_type = $_POST['savings_account_type'];
    $payment_plan = $_POST['payment_plan'];

    // Generate a 10-digit auto-generated application ID
    $application_id = mt_rand(1000000000, 9999999999);

    // Prepare the SQL statement
    $sql = "INSERT INTO savings_applications (member_id, application_id, name, email, phone_number, address, savings_amount, interest_rate, savings_term, savings_purpose, savings_account_type, payment_plan) 
            VALUES ('$member_id', '$application_id', '$name', '$email', '$phone_number', '$address', '$savings_amount', '$interest_rate', '$savings_term', '$savings_purpose', '$savings_account_type', '$payment_plan')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Application submitted successfully.'); window.location = 'index_member.php';</script>";
        exit; // Ensure no further code is executed
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // If form wasn't submitted properly
    $_SESSION['error_message'] = "Invalid form submission.";
    header('Location: index_member.php');
    exit();
}
?>
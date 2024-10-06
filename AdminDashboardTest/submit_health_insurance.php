<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "colens";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $member_id = $conn->real_escape_string($_POST['member_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $insurance_type = $conn->real_escape_string($_POST['insurance_type']);
    $coverage_amount = (float) $_POST['coverage_amount'];

    // New fields: payment_plan and coverage_term
    $payment_plan = $conn->real_escape_string($_POST['payment_plan']);
    $coverage_term = (int) $_POST['coverage_term']; // Assuming coverage_term is in months or years

    // Calculate payment_due based on payment_plan
    switch ($payment_plan) {
        case 'Monthly':
            $payment_due = $coverage_amount / ($coverage_term * 12); // Monthly payment
            break;
        case 'Quarterly':
            $payment_due = $coverage_amount / ($coverage_term * 4); // Quarterly payment
            break;
        case 'Annually':
            $payment_due = $coverage_amount / $coverage_term; // Annual payment
            break;
        default:
            $payment_due = 0; // Default case
            break;
    }

    // Generate a 10-digit auto-generated application ID with prefix 'hlt'
    $random_number = mt_rand(100000000, 999999999); // Generate a 9-digit random number
    $application_id = 'hlt' . $random_number; // Concatenate 'hlt' with the random number


    // Default status for a new application
    $status = 'Pending';

    // Insert health insurance application
    $sql = "INSERT INTO health_insurance_applications 
    (application_id, member_id, name, email, phone_number, address, birthday, insurance_type, coverage_amount, payment_plan, coverage_term, payment_due, status) 
    VALUES ('$application_id', '$member_id', '$name', '$email', '$phone_number', '$address', '$birthday', '$insurance_type', $coverage_amount, '$payment_plan', '$coverage_term', $payment_due, '$status')";

    if ($conn->query($sql) === TRUE) {
        // Handle beneficiaries
        if (!empty($_POST['beneficiary_name'])) {
            for ($i = 0; $i < count($_POST['beneficiary_name']); $i++) {
                $beneficiary_name = $conn->real_escape_string($_POST['beneficiary_name'][$i]);
                $beneficiary_relationship = $conn->real_escape_string($_POST['beneficiary_relationship'][$i]);
                $beneficiary_dob = $conn->real_escape_string($_POST['beneficiary_dob'][$i]);

                // Insert each beneficiary into the beneficiaries table
                $beneficiary_sql = "INSERT INTO beneficiaries (application_id, beneficiary_name, beneficiary_relationship, beneficiary_dob) 
                VALUES ('$application_id', '$beneficiary_name', '$beneficiary_relationship', '$beneficiary_dob')";

                if (!$conn->query($beneficiary_sql)) {
                    echo "Error inserting beneficiary: " . $conn->error;
                }
            }
        }

        echo "<script>alert('Health Insurance Application submitted successfully.'); window.location = 'index_member.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
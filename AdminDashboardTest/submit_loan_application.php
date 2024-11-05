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
    $collateral = $conn->real_escape_string($_POST['collateral']);
    $bank_info = isset($_POST['bank_info']) ? $conn->real_escape_string($_POST['bank_info']) : null;
    $employment_status = $conn->real_escape_string($_POST['employment_status']);
    $collateral_image = isset($_FILES['collateral_image']['tmp_name']) ? $_FILES['collateral_image']['tmp_name'] : null; // Handle upload
    $payment_plan = $conn->real_escape_string($_POST['payment_plan']);  // Collect the selected payment plan

    // New fields
    $bank_name = $conn->real_escape_string($_POST['bank_name']);
    $bank_id = $conn->real_escape_string($_POST['bank_id']);
    $branch = $conn->real_escape_string($_POST['branch']);

    // Generate a 10-digit auto-generated application ID with prefix 'loan'
    $random_number = mt_rand(100000000, 999999999); // Generate a 9-digit random number
    $application_id = 'loan' . $random_number; // Concatenate 'loan' with the random number

    // Default status for a new application
    $status = 'Pending';

    // Determine the interest rate based on loan term
    switch ($loan_term) {
        case 1:
            $interest_rate = (12 / 100) * $loan_amount;  // 2% interest rate for 1 year term
            break;
        case 3:
            $interest_rate = (20 / 100) * $loan_amount;  // 6% interest rate for 3 years term
            break;
        case 5:
            $interest_rate = (24 / 100) * $loan_amount; // 10% interest rate for 5 years term
            break;
        default:
            $interest_rate = 0;  // Default to 0 if the term doesn't match
            break;
    }



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
        if ($loan_amount > 30000) {
            echo "<script>alert('You can only loan up to 30,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 20000 && $contribution_amount < 30000) {
        if ($loan_amount > 60000) {
            echo "<script>alert('You can only loan up to 60,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 30000 && $contribution_amount < 40000) {
        if ($loan_amount > 90000) {
            echo "<script>alert('You can only loan up to 90,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 40000 && $contribution_amount < 50000) {
        if ($loan_amount > 120000) {
            echo "<script>alert('You can only loan up to 120,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 50000 && $contribution_amount < 60000) {
        if ($loan_amount > 150000) {
            echo "<script>alert('You can only loan up to 150,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 60000 && $contribution_amount < 70000) {
        if ($loan_amount > 180000) {
            echo "<script>alert('You can only loan up to 180,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 70000 && $contribution_amount < 80000) {
        if ($loan_amount > 210000) {
            echo "<script>alert('You can only loan up to 210,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 80000 && $contribution_amount < 90000) {
        if ($loan_amount > 240000) {
            echo "<script>alert('You can only loan up to 240,000.'); window.history.back();</script>";
            exit;
        }
    } elseif ($contribution_amount >= 90000) {
        if ($loan_amount > 270000) {
            echo "<script>alert('You can only loan up to 270,000.'); window.history.back();</script>";
            exit;
        }
    }

    $principal_amount = $loan_amount;
    // Calculate the final loan amount based on the interest rate
    $loan_amount = $loan_amount + $interest_rate;

    $total_size = strlen($application_id) + strlen($member_id) + strlen($name) + strlen($email) +
        strlen($phone_number) + strlen($address) +
        strlen($annual_income) + strlen($loan_amount) +
        strlen($bank_info) + strlen($loan_term) +
        strlen($interest_rate) + strlen($loan_purpose) +
        strlen($employment_status) + strlen($collateral_image) +
        strlen($payment_plan) + strlen($status) +
        strlen($supporting_document_1) + strlen($supporting_document_2);

    echo "Total query size: $total_size bytes<br>";



    // SQL query to insert data into loan_applications
    $sql = "INSERT INTO loan_applications (application_id, member_id, name, email, phone_number, address, collateral, annual_income, loan_amount, principal_amount, bank_name, bank_id, branch, loan_term, interest_rate, loan_purpose, employment_status, collateral_image, payment_plan, status, supporting_document_1, supporting_document_2)
            VALUES ('$application_id', '$member_id', '$name', '$email', '$phone_number', '$address', '$collateral', $annual_income, $loan_amount, $principal_amount, '$bank_name', '$bank_id', '$branch', $loan_term, $interest_rate, '$loan_purpose', '$employment_status', '$collateral_image', '$payment_plan', '$status', '$supporting_document_1', '$supporting_document_2')";


    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Application submitted successfully.'); window.location = 'index_member.php';</script>";

        echo "Application ID: $application_id<br>";

        // Retrieve the co-maker names array from POST data
        if (isset($_POST['comakers_name']) && !empty($_POST['comakers_name'])) {
            echo '<pre>';
            print_r($_POST['comakers_name']); // Debug: Print the co-maker names array
            echo '</pre>';

            foreach ($_POST['comakers_name'] as $comaker_name) {
                // Sanitize each co-maker name
                $comaker_name = $conn->real_escape_string($comaker_name);

                // Insert each co-maker into the comakers table
                $coMakerSql = "INSERT INTO comakers (application_id, comaker_name) VALUES ('$application_id', '$comaker_name')";

                if (!$conn->query($coMakerSql)) {
                    // Log query error
                    echo "Error inserting co-maker: " . $conn->error . "<br>";
                    echo "Query: " . $coMakerSql . "<br>";
                } else {
                    echo "Co-maker '$comaker_name' added successfully.<br>";
                }
            }
        } else {
            echo "No co-makers found.<br>"; // Debug: Check if the co-makers are missing
        }

        echo "<script>alert('Application submitted successfully, including co-makers.'); window.location = 'index_member.php';</script>";
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Close connection
    $conn->close();
}
?>
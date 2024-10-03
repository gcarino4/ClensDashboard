<?php
// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Database connection details
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

// Sanitize and validate input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['username']);
    $age = htmlspecialchars($_POST['age']);
    $birthday = htmlspecialchars($_POST['birthday']);
    $sex = htmlspecialchars($_POST['sex']);
    $civil_status = htmlspecialchars($_POST['civil_status']);
    $address = htmlspecialchars($_POST['address']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirmPassword']);
    $contact_no = htmlspecialchars($_POST['contact_no']);
    $member_id = htmlspecialchars($_POST['member_id']); // Retrieve member_id from hidden field

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
        exit;
    }

    // Check if the email already exists
    $sql = "SELECT id FROM members WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Email already exists!";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Autofill the role and verified status
    $role = "Member";
    $verified = "False";

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set date_of_creation to current timestamp and expiration to five years from now
    $date_of_creation = date("Y-m-d H:i:s");
    $expiration = date("Y-m-d", strtotime("+5 years"));

    $valid_id = htmlspecialchars($_POST['valid_id']); // Base64 encoded

    $sql = "INSERT INTO members (member_id, role, name, contact_no, age, birthday, sex, civil_status, address, email, password, verified, date_of_creation, expiration, valid_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssss", $member_id, $role, $name, $contact_no, $age, $birthday, $sex, $civil_status, $address, $email, $hashed_password, $verified, $date_of_creation, $expiration, $valid_id);

    if ($stmt->execute()) {
        echo "New record created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>
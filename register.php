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
    $birthday = htmlspecialchars($_POST['birthday']);
    $sex = htmlspecialchars($_POST['sex']);
    $civil_status = htmlspecialchars($_POST['civil_status']);
    $address = htmlspecialchars($_POST['address']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirmPassword']);
    $contact_no = htmlspecialchars($_POST['contact_no']);
    $member_id = htmlspecialchars($_POST['member_id']); // Retrieve member_id from hidden field

    // Calculate age from birthday
    $age = date_diff(date_create($birthday), date_create('now'))->y;  // Calculate age

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
        exit;
    }

    // Insert new member
    $sql = "INSERT INTO members (member_id, name, age, birthday, sex, civil_status, address, contact_no, email, password, role, verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Member', 'False')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssssss", $member_id, $name, $age, $birthday, $sex, $civil_status, $address, $contact_no, $email, password_hash($password, PASSWORD_DEFAULT));

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
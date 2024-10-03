<?php
include 'connection.php';

// Retrieve form data
$member_id = $_POST['member_id']; // Assuming you have this field in your form
$name = $_POST['name'];
$age = $_POST['age'];
$birthday = $_POST['birthday'];
$sex = $_POST['sex'];
$civil_status = $_POST['civil_status'];
$address = $_POST['address'];
$contact_no = $_POST['contact_no'];
$role = $_POST['role'];
$verified = $_POST['verified'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (member_id, name, age, birthday, sex, civil_status, address, contact_no, role, verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisssssss", $member_id, $name, $age, $birthday, $sex, $civil_status, $address, $contact_no, $role, $verified);

// Execute the statement
if ($stmt->execute()) {
    // Redirect to another page
    header("Location: user-account-management.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
<?php
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
        echo "<script>alert('Email already exists!'); window.location.href='user-account-management.php';</script>";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Generate member_id
    $date_prefix = date('Ymd');
    $member_id = $date_prefix . str_pad(rand(0, 999999), 10, '0', STR_PAD_LEFT);

    // Autofill the role
    $role = "Member";
    $verified = "False";
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into the database
    $sql = "INSERT INTO members (member_id, role, name, contact_no, age, birthday, sex, civil_status, address, email, password, verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssssssss", $member_id, $role, $name, $contact_no, $age, $birthday, $sex, $civil_status, $address, $email, $hashed_password, $verified);

    if ($stmt->execute()) {
        // Success: Redirect or notify the user
        echo "<script>alert('You have added a user successfully!'); window.location.href='user-account-management.php';</script>";
    } else {
        // Error: Notify the user
        echo "<script>alert('Error in adding new user. Please try again.'); window.location.href='user-account-management.php';";
    }


    // Close connections
    $stmt->close();
    $conn->close();
}
?>
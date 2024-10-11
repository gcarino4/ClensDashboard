<?php
namespace update_record;

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $birthday = $_POST['birthday'];
    $sex = $_POST['sex'];
    $civil_status = $_POST['status'];
    $address = $_POST['address'];
    $contact_no = $_POST['contact_number'];
    $role = $_POST['role'];
    $verified = $_POST['verified'];
    $date_of_creation = $_POST['date_of_creation'];

    // Prepare the SQL query to update the user record
    $sql = "UPDATE members 
        SET name = ?, 
            age = ?, 
            birthday = ?, 
            sex = ?, 
            civil_status = ?, 
            address = ?, 
            contact_no = ?, 
            role = ?, 
            verified = ?, 
            date_of_creation = ? 
        WHERE member_id = ?";


    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $stmt->bind_param("ssssssssssi", $name, $age, $birthday, $sex, $civil_status, $address, $contact_no, $role, $verified, $date_of_creation, $id);


    // Execute the query
    if ($stmt->execute()) {
        // Success: Redirect or notify the user
        echo "<script>alert('User record updated successfully!'); window.location.href='user_account_management.php';</script>";
    } else {
        // Error: Notify the user
        echo "<script>alert('Error updating user record: " . $stmt->error . "');</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If the request is not POST, redirect back to the management page
    header("Location: user_account_management.php");
    exit();
}
?>
<?php
namespace update_record;


include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $birthday = $_POST['birthday'];
    $sex = $_POST['sex'];
    $civil_status = $_POST['status']; // Make sure this matches your form field
    $address = $_POST['address'];
    $contact_no = $_POST['contact_number']; // Ensure field names match
    $role = $_POST['role'];
    $verified = $_POST['verified']; // Make sure this matches the form field

    // Debugging output
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";
    $sql = "UPDATE members 
    SET name = COALESCE(?, name), 
        age = COALESCE(?, age), 
        birthday = COALESCE(?, birthday), 
        sex = COALESCE(?, sex), 
        civil_status = COALESCE(?, civil_status), 
        address = COALESCE(?, address), 
        contact_no = COALESCE(?, contact_no), 
        role = COALESCE(?, role), 
        verified = COALESCE(?, verified) 
    WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssi", $name, $age, $birthday, $sex, $civil_status, $address, $contact_no, $role, $verified, $id);

    // Execute the query
    if ($stmt->execute()) {
        // Success: Redirect or notify the user
        echo "<script>alert('User record updated successfully!'); window.location.href='user_account_management.php';</script>";
    } else {
        // Error: Notify the user
        echo "<script>alert('Error updating user record. Please try again.');</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
header("Location: user-account-management.php");
?>
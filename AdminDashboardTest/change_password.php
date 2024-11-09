<?php
// Include database connection and check user session
require 'connection.php';
require 'check_user.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the user's current password from the database
    $member_id = $_SESSION['member_id'];
    $sql = "SELECT password FROM members WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            // Hash the new password and update in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE members SET password = ? WHERE member_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $member_id);
            if ($stmt->execute()) {
                // End the session after success
                session_unset(); // Unset all session variables
                session_destroy(); // Destroy the session

                // Echo the JavaScript alert and redirect
                echo "<script>
                        alert('Password changed successfully!');
                        window.location.href = 'https://colenstech.online/CLENSADMINDASHBOARD/cLensAdminDashboard/index.php';
                      </script>";
                exit(); // Ensure the script stops executing after the alert and redirect
            } else {
                echo "Error updating password!";
            }
        } else {
            echo "New passwords do not match!";
        }
    } else {
        echo "Current password is incorrect!";
    }

    $stmt->close();
    $conn->close();
}
?>
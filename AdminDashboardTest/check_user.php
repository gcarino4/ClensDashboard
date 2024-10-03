<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Use 'name' from session for displayName
    $displayName = isset($_SESSION['name']) ? $_SESSION['name'] : "Please Login";
    $role = ucfirst($_SESSION['role']);
} else {
    // Handle the case where the user is not logged in
    $displayName = "Guest";
    $role = "";
    // Display message and redirect
    echo "<script>alert('You have been logged out. Please log in again.'); window.location = '../index.php';</script>";
    exit; // Ensure no further code is executed
}
?>
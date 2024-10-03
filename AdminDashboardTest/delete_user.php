<?php
include 'connection.php';

// Check if the ID parameter is set
if (isset($_POST['id'])) {
    // Sanitize the ID parameter to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Define the SQL query to delete the record with the given ID
    $sql = "DELETE FROM members WHERE id = '$id'";

    // Execute the delete query
    if (mysqli_query($conn, $sql)) {
        // Deletion successful
        echo "Record deleted successfully";
    } else {
        // Deletion failed
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // If ID parameter is not set, return an error message
    echo "Error: No ID provided";
}

// Close the database connection
mysqli_close($conn);
?>
<?php
include 'connection.php';

if (isset($_POST['member_id'])) {
    // Sanitize the member_id to prevent SQL injection
    $member_id = mysqli_real_escape_string($conn, $_POST['member_id']);

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Check if the member exists
        $memberCheckQuery = "SELECT member_id FROM members WHERE member_id = '$member_id'";
        $result = mysqli_query($conn, $memberCheckQuery);

        if ($result && mysqli_num_rows($result) > 0) {
            // Member exists, proceed with deletion
            // 1. Delete the contribution record(s) from the contributions table
            $deleteContributionsSql = "DELETE FROM contributions WHERE member_id = '$member_id'";
            if (!mysqli_query($conn, $deleteContributionsSql)) {
                throw new Exception('Error deleting contributions: ' . mysqli_error($conn));
            }

            // 2. Delete the member record from the members table
            $deleteMemberSql = "DELETE FROM members WHERE member_id = '$member_id'";
            if (!mysqli_query($conn, $deleteMemberSql)) {
                throw new Exception('Error deleting member: ' . mysqli_error($conn));
            }

            // Commit the transaction
            mysqli_commit($conn);
            echo "Member and related contribution(s) deleted successfully";
        } else {
            // Member not found
            echo "Error: Member not found";
        }
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        mysqli_rollback($conn);
        echo "Error deleting member and contributions: " . $e->getMessage();
    }
} else {
    // If member_id parameter is not set, return an error message
    echo "Error: No member ID provided";
}

// Close the database connection
mysqli_close($conn);
?>
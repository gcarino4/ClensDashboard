<?php
include 'connection.php'; // Assuming this file contains your database connection details

if (isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];

    // Prepare the SQL query to fetch the member name based on the member_id
    $sql = "SELECT name FROM members WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $member_id);
    $stmt->execute();
    $stmt->bind_result($member_name);
    $stmt->fetch();

    if ($member_name) {
        echo $member_name;
    } else {
        echo ''; // No member found
    }

    $stmt->close();
    $conn->close();
}
?>
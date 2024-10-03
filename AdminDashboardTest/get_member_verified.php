<?php
// Assume user is logged in and you have their member_id stored in the session
$member_id = $_SESSION['member_id'];

include 'connection.php';

// Query to check if the user is verified
$sql = "SELECT verified FROM members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $member_id);
$stmt->execute();
$stmt->bind_result($verified);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
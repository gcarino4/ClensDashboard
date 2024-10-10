<?php
include 'connection.php';

$member_id = $_GET['member_id'];

// Fetch the member data from the database
$sql = "SELECT * FROM members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Member not found']);
}

$stmt->close();
$conn->close();
?>
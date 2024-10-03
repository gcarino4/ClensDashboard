<?php
include 'connection.php';

if (isset($_GET['member_id'])) {
    $member_id = $_GET['member_id'];

    // Prepare SQL query to fetch the member name
    $sql = "SELECT name FROM members WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'member_name' => $row['name']]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}

$conn->close();
?>
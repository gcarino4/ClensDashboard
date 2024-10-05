<?php
include 'connection.php';

// Ensure session is started and member_id is set
if (!isset($_SESSION['member_id'])) {
    die('Member ID is not set in the session.');
}

// Get the current member_id from the session
$member_id = $_SESSION['member_id'];

// Query to select pending applications only for the current member_id
$sql = "SELECT application_id, member_id, application_type FROM pending_applications WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $member_id); // Bind the session member_id as a parameter
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Applications</title>
    <style>
        table {
            width: 100%;
            border: 1px;
        }
    </style>
</head>

<body>

    <table>
        <thead>
            <tr>
                <th>Application ID</th>
                <th>Member ID</th>
                <th>Application Type</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["application_id"] . "</td>";
                    echo "<td>" . $row["member_id"] . "</td>";
                    echo "<td>" . ucfirst($row["application_type"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No pending applications found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>

</html>

<?php
// Close the statement and the connection
$stmt->close();
$conn->close();
?>
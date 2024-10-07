<?php
include 'connection.php';


$session_member_id = $_SESSION['member_id']; // Get the current member_id from the session
$session_role = $_SESSION['role']; // Get the current user's role from the session

// Check if the user is an Admin or Member
if ($session_role === 'Admin') {
    // Admin: Fetch all contribution payment records
    $sql = "SELECT * FROM contribution_payments"; // Ensure this table name is correct
    $stmt = $conn->prepare($sql);
} else {
    // Member: Fetch contribution payment records only for the current member
    $sql = "SELECT * FROM contribution_payments WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_member_id);
}

$stmt->execute();
$result = $stmt->get_result();

echo '<table border="1" id="paymentsTable">';
echo '<tr>';
echo '<th>Contribution Transaction Number</th>';
echo '<th>Payment Amount</th>';
echo '<th>Payment Date</th>';
echo '<th>Proof of Payment</th>';
echo '</tr>';

if ($result->num_rows > 0) {
    while ($payment = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($payment['contribution_id']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_date']) . '</td>'; // Ensure you have a column for payment_date in your database

        // Convert Base64 text to an image
        if (!empty($payment['payment_image'])) {
            // Directly use the Base64 string
            $imageSrc = htmlspecialchars($payment['payment_image']); // Make sure this is valid Base64
            echo '<td><img src="' . $imageSrc . '" alt="Payment Proof" style="width:100px;height:auto;"></td>';
        } else {
            echo '<td>No image available</td>';
        }

        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">No payment records found.</td></tr>'; // Updated column span to match header count
}

echo '</table>';

// Close the prepared statement
$stmt->close();
$conn->close();
?>
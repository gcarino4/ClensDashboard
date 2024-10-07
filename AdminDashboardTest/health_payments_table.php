<?php
include 'connection.php';

$session_member_id = $_SESSION['member_id']; // Get the current member_id from the session
$session_role = $_SESSION['role'];

if ($session_role === 'Admin') {
    $sql = "SELECT * FROM health_payments";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM health_payments WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_member_id);

}

$stmt->execute();
$result = $stmt->get_result();

echo '<table border="1" id="paymentsTable">';
echo '<tr>';
echo '<th>Transaction Number</th>';
echo '<th>Application ID</th>';
echo '<th>Payment Amount</th>';
echo '<th>Payment Due</th>';
echo '<th>Payment Date</th>';
echo '</tr>';

if ($result->num_rows > 0) {
    while ($payment = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($payment['transaction_number']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['application_id']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_due']) . '</td>';
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
    echo '<tr><td colspan="5">No payment records found for this member.</td></tr>';
}

echo '</table>';

// Close the prepared statement
$stmt->close();
$conn->close();
?>
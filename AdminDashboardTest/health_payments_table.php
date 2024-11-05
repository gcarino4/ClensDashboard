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
echo '<div style="max-height: 40vh; overflow-y: auto;">';
echo '<table border="1" id="paymentsTable">';
echo '<tr>';
echo '<th>Transaction Number</th>';
echo '<th>Application ID</th>';
echo '<th>Payment Amount</th>';
echo '<th>Status</th>';
echo '<th>Payment Due</th>';
echo '<th>Payment Date</th>';
if ($session_role === 'Admin') {
    echo '<th>Action</th>';  // Only add "Action" column header if role is Admin
}
echo '</tr>';

if ($result->num_rows > 0) {
    while ($payment = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($payment['transaction_number']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['application_id']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['status']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_due']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_date']) . '</td>';

        // Convert Base64 text to an image
        if (!empty($payment['payment_image'])) {
            $imageSrc = htmlspecialchars($payment['payment_image']); // Directly use the base64 string
            echo '<td><img src="' . $imageSrc . '" alt="Payment Proof" style="width:100px;height:auto;"></td>';
        } else {
            echo '<td>No image available</td>';
        }

        // Only show action buttons if the role is Admin
        if ($session_role === 'Admin') {
            if ($payment['status'] === 'pending') {
                echo '<td>';
                echo '<button class="approveBtn" onclick="updatePaymentStatus(\'' . $payment['transaction_number'] . '\', \'approved\')">Approve</button>';
                echo '<button class="rejectBtn" onclick="updatePaymentStatus(\'' . $payment['transaction_number'] . '\', \'rejected\')">Reject</button>';
                echo '</td>';
            } else {
                echo '<td>No actions needed</td>';
            }
        }

        echo '</tr>';
    }
} else {
    $colspan = $session_role === 'Admin' ? 6 : 5;  // Adjust colspan based on role
    echo '<tr><td colspan="' . $colspan . '">No payment records found for this member.</td></tr>';
}

echo '</table>';
echo '</div>';
// Close the prepared statement
$stmt->close();
$conn->close();
?>

<script>
    function updatePaymentStatus(transaction_number, status) {
        fetch('update_health_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ transaction_number, status })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Payment status updated successfully.');
                    location.reload(); // Reload the page to show updated status
                } else {
                    alert('Error updating payment status: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
<?php

include 'connection.php';

$session_member_id = $_SESSION['member_id']; // Get the current member_id from the session
$session_role = $_SESSION['role']; // Get the current user's role from the session

// Check if the user is an Admin or Member
if ($session_role === 'Admin') {
    // Admin: Fetch all loan payment records
    $sql = "SELECT * FROM loan_payments";
    $stmt = $conn->prepare($sql);
} else {
    // Member: Fetch loan payment records only for the current member
    $sql = "SELECT * FROM loan_payments WHERE member_id = ?";
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
echo '<th>Updated Loan Amount</th>';
echo '<th>Payment Date</th>';
echo '<th>Proof of Payment</th>';

// Add a column header for Admin actions
if ($session_role === 'Admin') {
    echo '<th>Actions</th>';
}
echo '</tr>';

if ($result->num_rows > 0) {
    while ($payment = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($payment['transaction_number']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['application_id']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['status']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['updated_loan_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_date']) . '</td>';

        // Convert Base64 text to an image
        if (!empty($payment['payment_image'])) {
            $imageSrc = 'data:image/jpeg;base64,' . htmlspecialchars($payment['payment_image']);
            echo '<td><img src="' . $imageSrc . '" alt="Payment Proof" style="width:100px;height:auto;"></td>';
        } else {
            echo '<td>No image available</td>';
        }

        // Add action buttons for Admin
        if ($session_role === 'Admin') {
            echo '<td>';
            if ($payment['status'] === 'pending') {
                echo '<button class="approveBtn" onclick="updateStatus(\'' . $payment['transaction_number'] . '\', \'approved\')">Approve</button>';
                echo ' ';
                echo '<button class="rejectBtn" onclick="updateStatus(\'' . $payment['transaction_number'] . '\', \'rejected\')">Reject</button>';
            } else {
                echo 'No actions needed';
            }
            echo '</td>';
        }

        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="8">No payment records found.</td></tr>'; // Updated column span to match header count
}

echo '</table>';
echo '</div>';

// Close the prepared statement
$stmt->close();
$conn->close();
?>

<script>
    // JavaScript function to update payment status
    function updateStatus(transactionNumber, status) {
        fetch('update_loan_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ transaction_number: transactionNumber, status: status })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Status updated successfully.");
                    location.reload(); // Reload page to see updated status
                } else {
                    alert("Error updating status: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
    }
</script>
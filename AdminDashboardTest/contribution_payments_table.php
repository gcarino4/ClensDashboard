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

echo '<div style="max-height: 40vh; overflow-y: auto;">';
echo '<table border="1" id="paymentsTable">';
echo '<tr>';
echo '<th>Contribution Transaction Number</th>';
echo '<th>Payment Amount</th>';
echo '<th>Status</th>';
echo '<th>Payment Date</th>';
echo '<th>Proof of Payment</th>';

// Display "Action" column header only if the role is Admin
if ($session_role === 'Admin') {
    echo '<th>Action</th>';
}
echo '</tr>';

if ($result->num_rows > 0) {
    while ($payment = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($payment['contribution_id']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['status']) . '</td>';
        echo '<td>' . htmlspecialchars($payment['payment_date']) . '</td>';

        // Convert Base64 text to an image
        if (!empty($payment['payment_image'])) {
            $imageSrc = htmlspecialchars($payment['payment_image']); // Directly use the base64 string
            echo '<td><img src="' . $imageSrc . '" alt="Payment Proof" style="width:100px;height:auto;"></td>';
        } else {
            echo '<td>No image available</td>';
        }


        // Show "Approve" button only if the role is Admin and status is not already approved
        if ($session_role === 'Admin') {
            if ($payment['status'] === 'pending') {
                echo '<td><button class="approveBtn" onclick="approvePayment(\'' . $payment['contribution_id'] . '\', ' . $payment['payment_amount'] . ')">Approve</button></td>';
                echo '<td><button class="rejectBtn" onclick="rejectPayment(\'' . $payment['contribution_id'] . '\', ' . $payment['payment_amount'] . ')">Reject</button></td>';
            } else {
                echo '<td>No actions needed</td>';
            }

        }

        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="' . ($session_role === 'Admin' ? 5 : 4) . '">No payment records found.</td></tr>'; // Updated column span
}

echo '</table>';
echo '</div>';

// Close the prepared statement
$stmt->close();
$conn->close();

?>
<script>
    function approvePayment(contribution_id, payment_amount) {
        // Send AJAX request to approve the payment and update the contributions table
        fetch('update_contribution_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ contribution_id, payment_amount, status: 'approved' }) // Include status
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment approved successfully.');
                    location.reload(); // Reload the page to show updated status
                } else {
                    alert('Error approving payment: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function rejectPayment(contribution_id, payment_amount) {
        // Send AJAX request to reject the payment and update the contributions table
        fetch('update_contribution_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ contribution_id, payment_amount, status: 'rejected' }) // Include status
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment rejected successfully.');
                    location.reload(); // Reload the page to show updated status
                } else {
                    alert('Error rejecting payment: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }


</script>
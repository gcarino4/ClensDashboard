<?php
namespace approved_health_insurance;

include 'connection.php';

// Ensure session is started and member_id is set
if (!isset($_SESSION['member_id'])) {
    die('Member ID is not set in the session.');
}

$session_member_id = $_SESSION['member_id'];

// Step 1: Query the `health_insurance_applications` table for approved insurance
$sql = "SELECT application_id, member_id, insurance_type, coverage_amount, payment_plan, coverage_term, payment_due
        FROM health_insurance_applications 
        WHERE member_id = ? AND status = 'approved'";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare statement for health_insurance_applications: " . $conn->error);
}

$stmt->bind_param("s", $session_member_id);
$stmt->execute();
$result = $stmt->get_result();

// Step 2: Insert the approved insurance into the `approved_health_insurance` table
if ($result->num_rows > 0) {
    while ($insurance = $result->fetch_assoc()) {
        // Insert into approved_health_insurance
        $insert_sql = "INSERT IGNORE INTO approved_health_insurance (application_id, member_id, insurance_type, coverage_amount, payment_plan, coverage_term, payment_due) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);

        if ($insert_stmt) {
            $insert_stmt->bind_param(
                "sssssss",
                $insurance['application_id'],
                $insurance['member_id'],
                $insurance['insurance_type'],
                $insurance['coverage_amount'],
                $insurance['payment_plan'],
                $insurance['coverage_term'],
                $insurance['payment_due']
            );
            $insert_stmt->execute();
        } else {
            echo "Error preparing insert statement: " . $conn->error;
        }
    }
} else {
    echo '<p>No approved health insurance applications found.</p>';
}
$stmt->close();

// Step 3: Display the approved insurance from `approved_health_insurance`
$sql = "SELECT application_id, member_id, insurance_type, coverage_amount, payment_plan, coverage_term, payment_due 
        FROM approved_health_insurance 
        WHERE member_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare statement for approved_health_insurance: " . $conn->error);
}

$stmt->bind_param("s", $session_member_id);
$stmt->execute();
$result = $stmt->get_result();

// Output insurance data in a table
echo '<table border="1" id="insuranceTable">';
echo '<tr>';
echo '<th>Application ID</th>';
echo '<th>Member ID</th>';
echo '<th>Insurance Type</th>';
echo '<th>Coverage Amount</th>';
echo '<th>Payment Plan</th>';
echo '<th>Coverage Term</th>';
echo '<th>Payment Due</th>';
echo '</tr>';

if ($result->num_rows > 0) {
    while ($insurance = $result->fetch_assoc()) {
        echo '<tr onclick="openhealthPaymentModal(\'' . htmlspecialchars($insurance['application_id']) . '\', ' . htmlspecialchars($insurance['payment_due']) . ')">';
        echo '<td>' . htmlspecialchars($insurance['application_id']) . '</td>';
        echo '<td>' . htmlspecialchars($insurance['member_id']) . '</td>';
        echo '<td>' . htmlspecialchars($insurance['insurance_type']) . '</td>';
        echo '<td>' . htmlspecialchars($insurance['coverage_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($insurance['payment_plan']) . '</td>';
        echo '<td>' . htmlspecialchars($insurance['coverage_term']) . '</td>';
        echo '<td>' . htmlspecialchars($insurance['payment_due']) . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="7">No approved health insurance found.</td></tr>';
}

echo '</table>';
?>

<!-- Payment Modal -->
<div id="healthPaymentModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background-color:#fff; margin:100px auto; padding:20px; width:300px;">
        <h5>Process Payment</h5>

        <div>
            <form id="healthPaymentForm">
                <div>
                    <label for="applicationId">Application ID</label>
                    <input type="text" id="applicationId" name="application_id" readonly>
                </div>
                <div>
                    <label for="paymentAmount">Payment Amount</label>
                    <input type="number" id="paymentAmount" name="payment_amount" required>
                </div>
                <div>
                    <label for="paymentDate">Payment Date</label>
                    <input type="date" id="paymentDate" name="payment_date" required>
                </div>
                <div>
                    <label for="paymentNotes">Notes</label>
                    <textarea id="paymentNotes" name="payment_notes"></textarea>
                </div>
                <button type="submit">Submit Payment</button>
                <button onclick="closehealthPaymentModal()">Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to open the payment modal and set the current date
    function openhealthPaymentModal(applicationId, paymentValue) {
        document.getElementById('applicationId').value = applicationId;
        document.getElementById('paymentAmount').value = paymentValue;

        // Set the current date as the default value for the payment date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('paymentDate').value = today;

        document.getElementById('healthPaymentModal').style.display = 'block';
    }

    // Function to close the payment modal
    function closehealthPaymentModal() {
        document.getElementById('healthPaymentModal').style.display = 'none';
    }

    // Handle form submission
    // Handle form submission
    document.getElementById('healthPaymentForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);
        const paymentDue = parseFloat(document.getElementById('paymentAmount').value); // This line should get the correct value instead
        // Use this as a placeholder

        // Check if payment amount is valid
        if (paymentAmount !== paymentDue) { // Changed from < or > to != for equality check
            alert('Payment amount must be equal to the payment due.');
            return; // Stop submission if validation fails
        }

        const formData = new FormData(this); // Get form data

        fetch('health_process_payment.php', { // Adjust the path as needed
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment recorded successfully.');
                    closehealthPaymentModal(); // Close modal on success
                    // Optionally, refresh the table or update the UI
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });


</script>
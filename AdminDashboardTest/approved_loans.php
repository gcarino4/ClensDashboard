<?php
include 'connection.php';

// Ensure session is started and member_id is set
if (!isset($_SESSION['member_id'])) {
    die('Member ID is not set in the session.');
}

$session_member_id = $_SESSION['member_id'];

// Step 1: Query the `loan_applications` table for approved loans
$sql = "SELECT application_id, member_id, loan_term, payment_plan, loan_amount 
        FROM loan_applications 
        WHERE member_id = ? AND status = 'approved'";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Failed to prepare statement for loan_applications: " . $conn->error);
}

$stmt->bind_param("s", $session_member_id);
$stmt->execute();
$result = $stmt->get_result();

// Step 2: Insert the approved loans into the `approved_loans` table
if ($result->num_rows > 0) {
    while ($loan = $result->fetch_assoc()) {
        // Check if the loan already exists in approved_loans to avoid duplicate inserts
        $check_sql = "SELECT * FROM approved_loans WHERE application_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $loan['application_id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) { // If the loan is not already in approved_loans
            // Insert into approved_loans
            $insert_sql = "INSERT INTO approved_loans (application_id, member_id, loan_term, payment_plan, loan_amount) 
                           VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);

            if ($insert_stmt) {
                $insert_stmt->bind_param(
                    "sssss",
                    $loan['application_id'],
                    $loan['member_id'],
                    $loan['loan_term'],
                    $loan['payment_plan'],
                    $loan['loan_amount']
                );
                $insert_stmt->execute();
            } else {
                echo "Error preparing insert statement: " . $conn->error;
            }
        }
        $check_stmt->close();
    }
} else {
    echo '<p>No approved loans found in loan_applications.</p>';
}
$stmt->close();

// Step 3: Display the loans from `approved_loans`
$sql = "SELECT application_id, member_id, loan_term, payment_plan, loan_amount 
        FROM approved_loans 
        WHERE member_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Failed to prepare statement for approved_loans: " . $conn->error);
}

$stmt->bind_param("s", $session_member_id);
$stmt->execute();
$result = $stmt->get_result();

// Output loans in a table
echo '<table border="1" id="loansTable">';
echo '<tr>';
echo '<th>Application ID</th>';
echo '<th>Member ID</th>';
echo '<th>Loan Term</th>';
echo '<th>Payment Plan</th>';
echo '<th>Loan Amount</th>';
echo '</tr>';

if ($result->num_rows > 0) {
    while ($loan = $result->fetch_assoc()) {
        echo '<tr data-application-id="' . htmlspecialchars($loan['application_id']) . '" data-member-id="' . htmlspecialchars($loan['member_id']) . '" data-loan-amount="' . htmlspecialchars($loan['loan_amount']) . '">';
        echo '<td>' . htmlspecialchars($loan['application_id']) . '</td>';
        echo '<td>' . htmlspecialchars($loan['member_id']) . '</td>';
        echo '<td>' . htmlspecialchars($loan['loan_term']) . '</td>';
        echo '<td>' . htmlspecialchars($loan['payment_plan']) . '</td>';
        echo '<td class="loan-amount">' . htmlspecialchars($loan['loan_amount']) . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="5">No approved loans found.</td></tr>';
}

$stmt->close();
echo '</table>';
?>



<!-- Modal HTML -->
<div id="loanPaymentModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div>
        <div style="background-color:#fff; margin:100px auto; padding:20px; width:300px;">
            <h5>Process Payment</h5>
            <form id="paymentForm">
                <input type="hidden" name="application_id" id="modalApplicationId">
                <input type="hidden" name="member_id" id="modalMemberId">
                <label for="paymentAmount">Payment Amount:</label>
                <input type="number" name="payment_amount" id="paymentAmount" required>
                <button type="submit">Submit Payment</button>
                <button type="button" onclick="closeModal()">Cancel</button>
        </div>
        </form>
    </div>
</div>

<script>
    var loansTable = document.getElementById('loansTable');

    loansTable.addEventListener('click', function (event) {
        const row = event.target.closest('tr');
        if (row) {
            const applicationId = row.getAttribute('data-application-id');
            const memberId = row.getAttribute('data-member-id');
            const loanAmount = row.querySelector('.loan-amount').textContent;

            document.getElementById('modalApplicationId').value = applicationId;
            document.getElementById('modalMemberId').value = memberId;
            document.getElementById('paymentAmount').setAttribute('max', loanAmount); // Set max payment to loan amount

            document.getElementById('loanPaymentModal').style.display = 'block'; // Show the modal
        }
    });

    function closeModal() {
        document.getElementById('loanPaymentModal').style.display = 'none'; // Hide the modal
    }

    document.getElementById('paymentForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent form submission

        const applicationId = document.getElementById('modalApplicationId').value;
        const memberId = document.getElementById('modalMemberId').value;
        const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);

        // Send payment data to the server using fetch
        fetch('loan_process_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                application_id: applicationId,
                member_id: memberId,
                payment_amount: paymentAmount
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment processed successfully!');

                    // Update loan amount display in the table
                    const loanRow = document.querySelector(`tr[data-application-id="${applicationId}"]`);
                    const currentAmountCell = loanRow.querySelector('.loan-amount');
                    let currentAmount = parseFloat(currentAmountCell.textContent);
                    let newAmount = currentAmount - paymentAmount;

                    // Ensure that newAmount does not go below zero
                    if (newAmount < 0) {
                        newAmount = 0;
                    }

                    // Update displayed loan amount
                    currentAmountCell.textContent = newAmount.toFixed(2);

                    // Optionally remove the row if the loan amount is 0
                    if (newAmount === 0) {
                        loanRow.remove();
                    }

                    closeModal();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error processing payment:', error);
            });
    });
</script>
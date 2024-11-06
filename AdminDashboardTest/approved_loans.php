<?php

include 'connection.php';

// Ensure session is started and member_id is set
if (!isset($_SESSION['member_id'])) {
    die('Member ID is not set in the session.');
}

$session_member_id = $_SESSION['member_id'];



// Step 3: Display the loans from `approved_loans`
$sql = "SELECT * 
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
echo '<th>Principal Amount</th>';
echo '<th>Payment Due</th>';
echo '<th>Next Payment Due Date</th>';
echo '<th>Interest Amount</th>'; // Add interest rate column
echo '<th>Action</th>'; // New Action column for payment button
echo '</tr>';

if ($result->num_rows > 0) {
    while ($loan = $result->fetch_assoc()) {
        echo '<tr data-application-id="' . htmlspecialchars($loan['application_id']) . '" 
              data-member-id="' . htmlspecialchars($loan['member_id']) . '" 
              data-loan-amount="' . htmlspecialchars($loan['loan_amount']) . '" 
              data-principal-amount="' . htmlspecialchars($loan['principal_amount']) . '"
              data-minimum-payment="' . htmlspecialchars($loan['minimum_payment']) . '"
              data-interest-rate="' . htmlspecialchars($loan['interest_rate']) . '"  
              data-loan-term="' . htmlspecialchars($loan['loan_term']) . '"
              data-payment-plan="' . htmlspecialchars($loan['payment_plan']) . '"
              data-next-payment-due-date="' . htmlspecialchars($loan['next_payment_due_date']) . '">';

        echo '<td>' . htmlspecialchars($loan['application_id']) . '</td>';
        echo '<td>' . htmlspecialchars($loan['member_id']) . '</td>';
        echo '<td>' . htmlspecialchars($loan['loan_term']) . '</td>';
        echo '<td>' . htmlspecialchars($loan['payment_plan']) . '</td>';
        echo '<td class="loan-amount">' . htmlspecialchars($loan['loan_amount']) . '</td>';
        echo '<td>' . htmlspecialchars(($loan['principal_amount'])) . '</td>';
        echo '<td>' . htmlspecialchars(($loan['minimum_payment'])) . '</td>';
        echo '<td>' . htmlspecialchars($loan['next_payment_due_date']) . '</td>';

        echo '<td>' . htmlspecialchars(($loan['interest_rate'])) . '</td>'; // Display interest rate
        echo '<td><button class="openLoanModal">Make Payment</button></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="8">No approved loans found.</td></tr>';
}

echo '</table>';
?>



<!-- Modal HTML -->
<div id="loanPaymentModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div class="modal-content">
        <span class="close" onclick="closeLoanModal()">&times;</span>
        <h2>Make a Payment</h2>
        <form id="paymentForm">
            <input type="text" id="modalApplicationId" hidden>
            <input type="text" id="modalMemberId" readonly>

            <!-- Late Payment Message -->
            <p id="latePaymentMessage" style="color: red; display: none;">Late Payment: A 1% late fee has been added.
            </p>

            <label for="minimumPayment">Payment Due:</label>
            <input type="number" id="minimumPayment" readonly>

            <label for="nextPaymentDueDate">Next Payment Due Date:</label>
            <input type="text" id="nextPaymentDueDate" readonly> <!-- New field for next payment due date -->

            <label for="paymentAmount">Payment Amount:</label>
            <input type="number" id="paymentAmount" required>

            <label for="paymentImage">Upload Payment Image:</label>
            <input type="file" id="paymentImage" accept="image/*" required>

            <button type="submit">Submit Payment</button>
        </form>
    </div>
</div>

<script>
    var loansTable = document.getElementById('loansTable');

    document.querySelectorAll('.openLoanModal').forEach(button => {
        button.addEventListener('click', function (event) {
            const row = event.target.closest('tr');
            const applicationId = row.getAttribute('data-application-id');
            const memberId = row.getAttribute('data-member-id');
            const loanAmount = parseFloat(row.getAttribute('data-loan-amount'));
            const minimumPayment = parseFloat(row.getAttribute('data-minimum-payment')) || 0;
            const nextPaymentDueDate = row.getAttribute('data-next-payment-due-date');

            document.getElementById('modalApplicationId').value = applicationId;
            document.getElementById('modalMemberId').value = memberId;

            // Check if today's date is greater than nextPaymentDueDate
            const today = new Date();
            const dueDate = new Date(nextPaymentDueDate);
            let paymentDue = minimumPayment;
            const latePaymentMessage = document.getElementById('latePaymentMessage');

            if (today > dueDate) {
                const overdueFee = minimumPayment * 0.01; // 1% of minimumPayment
                paymentDue += overdueFee;
                latePaymentMessage.style.display = 'block'; // Show late payment message
            } else {
                latePaymentMessage.style.display = 'none'; // Hide late payment message if not overdue
            }

            // Display the calculated payment_due in the modal
            document.getElementById('minimumPayment').value = paymentDue.toFixed(2); // Show 2 decimal places
            document.getElementById('nextPaymentDueDate').value = nextPaymentDueDate;

            document.getElementById('loanPaymentModal').style.display = 'block';
        });
    });

    function closeLoanModal() {
        document.getElementById('loanPaymentModal').style.display = 'none';
    }


    function closeLoanModal() {
        document.getElementById('loanPaymentModal').style.display = 'none'; // Hide the modal
    }

    // Handle payment submission
    document.getElementById('paymentForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent form submission

        const applicationId = document.getElementById('modalApplicationId').value;
        const memberId = document.getElementById('modalMemberId').value;
        const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);

        // Get the uploaded image
        const paymentImageInput = document.getElementById('paymentImage');
        const paymentImageFile = paymentImageInput.files[0];

        // Check if an image is uploaded
        if (!paymentImageFile) {
            alert("Please upload a payment image.");
            return;
        }

        // Create a FileReader to convert the image file to Base64
        const reader = new FileReader();
        reader.onloadend = function () {
            const paymentImageBase64 = reader.result.split(',')[1]; // Get Base64 data without metadata

            // Send payment data to the server using fetch
            fetch('loan_process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    application_id: applicationId,
                    member_id: memberId,
                    payment_amount: paymentAmount,
                    payment_image_base64: paymentImageBase64 // Include Base64-encoded image

                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment processed successfully! Transaction Number: ' + data.transaction_number);
                        closeLoanModal();
                        location.reload(true);
                    } else {
                        alert('Error: ' + data.message); // Display the error message for overdue payment
                    }
                })
                .catch(error => {
                    console.error('Error processing payment:', error);
                });
        };

        // Read the image file as a Base64-encoded string
        reader.readAsDataURL(paymentImageFile);
    });
</script>
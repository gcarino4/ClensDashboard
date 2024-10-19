<?php

include 'connection.php';

// Ensure session is started and member_id is set
if (!isset($_SESSION['member_id'])) {
    die('Member ID is not set in the session.');
}

$session_member_id = $_SESSION['member_id'];

// Step 1: Query the `loan_applications` table for approved loans, including interest rate
// Step 1: Query the `loan_applications` table for approved loans, including interest rate and principal amount
$sql = "SELECT application_id, member_id, loan_term, payment_plan, loan_amount, due_date, interest_rate, principal_amount
        FROM loan_applications 
        WHERE member_id = ? AND status = 'approved'";


$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare statement for loan_applications: " . $conn->error);
}

$stmt->bind_param("s", $session_member_id);
$stmt->execute();
$result = $stmt->get_result();

$approvedLoans = [];
if ($result->num_rows > 0) {
    while ($loan = $result->fetch_assoc()) {
        // Calculate next payment due date based on payment plan
        $currentDueDate = new DateTime($loan['due_date']);
        $paymentPlan = $loan['payment_plan'];
        $loanTerm = $loan['loan_term'];

        // Adjust due date based on payment plan
        if ($paymentPlan === 'monthly') {
            $currentDueDate->modify('+1 month');
        } elseif ($paymentPlan === 'quarterly') {
            $currentDueDate->modify('+3 months');
        } elseif ($paymentPlan === 'annually') {
            $currentDueDate->modify('+1 year');
        }

        // Ensure due dates do not exceed loan term
        $loanStartDate = new DateTime($loan['due_date']);
        $loanEndDate = clone $loanStartDate;

        if ($loanTerm == '1 year') {
            $loanEndDate->modify('+1 year');
        } elseif ($loanTerm == '3 years') {
            $loanEndDate->modify('+3 years');
        } elseif ($loanTerm == '5 years') {
            $loanEndDate->modify('+5 years');
        }

        if ($currentDueDate > $loanEndDate) {
            $currentDueDate = $loanEndDate;
        }

        $loan['next_payment_due_date'] = $currentDueDate->format('Y-m-d');
        $approvedLoans[] = $loan;
    }
}
$stmt->close();

// Step 2: Insert the approved loans into the `approved_loans` table if not already present, including interest rate
foreach ($approvedLoans as $loan) {
    $check_sql = "SELECT * FROM approved_loans WHERE application_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $loan['application_id']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        // Step 2: Insert the approved loans into the `approved_loans` table if not already present, including interest rate and principal amount
        $insert_sql = "INSERT INTO approved_loans (application_id, member_id, loan_term, payment_plan, loan_amount, next_payment_due_date, interest_rate, principal_amount) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if ($insert_stmt) {
            $insert_stmt->bind_param(
                "ssssssss",
                $loan['application_id'],
                $loan['member_id'],
                $loan['loan_term'],
                $loan['payment_plan'],
                $loan['loan_amount'],
                $loan['next_payment_due_date'],
                $loan['interest_rate'],
                $loan['principal_amount'] // Insert principal amount here
            );
            $insert_stmt->execute();
        } else {
            echo "Error preparing insert statement: " . $conn->error;
        }
    }
    $check_stmt->close();
}

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

            <label for="paymentDue">Payment Due:</label>
            <input type="number" id="paymentDue" readonly>


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
            const loanAmount = parseFloat(row.getAttribute('data-loan-amount')); // Ensure loanAmount is a float
            const loanTerm = parseInt(row.getAttribute('data-loan-term')); // Assuming loanTerm is in years
            const paymentPlan = row.getAttribute('data-payment-plan');
            const principalAmount = parseFloat(row.getAttribute('data-principal-amount')) || 0; // Ensure principalAmount is a float
            const interestRate = parseFloat(row.getAttribute('data-interest-rate')) || 0; // Ensure interestRate is a float and convert to decimal
            const nextPaymentDueDate = row.getAttribute('data-next-payment-due-date');

            document.getElementById('modalApplicationId').value = applicationId;
            document.getElementById('modalMemberId').value = memberId;

            // Set max payment to loan amount
            document.getElementById('paymentAmount').setAttribute('max', loanAmount);

            // Calculate the payment_due
            let paymentPlanFactor;
            if (paymentPlan === 'monthly') {
                paymentPlanFactor = 12; // 12 months in a year
            } else if (paymentPlan === 'quarterly') {
                paymentPlanFactor = 4; // 4 quarters in a year
            } else if (paymentPlan === 'annually') {
                paymentPlanFactor = 1; // 1 year
            } else {
                paymentPlanFactor = 1; // Default to 1 for safety
            }



            const paymentDue = (interestRate + principalAmount) / (paymentPlanFactor * loanTerm);



            // Display the calculated payment_due in the modal
            document.getElementById('paymentDue').value = paymentDue.toFixed(0); // Show 2 decimal places

            // Show the modal
            document.getElementById('loanPaymentModal').style.display = 'block';
        });
    });

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
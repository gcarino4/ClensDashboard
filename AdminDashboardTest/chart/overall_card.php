<?php
// Database connection
include 'connection.php';

// Query to get the sum of payments made from loan_payments
$sql_loan_payments = "SELECT SUM(payment_amount) as total_loaned_paid FROM loan_payments";

$result_loan_payments = $conn->query($sql_loan_payments);

if ($result_loan_payments->num_rows > 0) {
    $row_loan_payments = $result_loan_payments->fetch_assoc();
    $total_loaned_paid = $row_loan_payments['total_loaned_paid'];
}

// Query to total amount_due from receivable table
$sql_amount_due = "SELECT SUM(amount_paid) as total_due FROM receivable";
$result_due = $conn->query($sql_amount_due);


if ($result_due->num_rows > 0) {
    $row_due = $result_due->fetch_assoc();
    $total_due = $row_due['total_due'];
}

$total_due = $total_due + $total_loaned_paid;


// Query to total amount from payments table
$sql_payments = "SELECT SUM(amount) as total_payments FROM payments";
$result_payments = $conn->query($sql_payments);

$total_payments = 0;
if ($result_payments->num_rows > 0) {
    $row_payments = $result_payments->fetch_assoc();
    $total_payments = $row_payments['total_payments'];
}

// Query to total amount from payments table
$sql_loans = "SELECT SUM(loan_amount) as total_loans FROM loan_applications";
$result_loans = $conn->query($sql_loans);
$total_loans = 0;
if ($result_loans->num_rows > 0) {
    $row_loans = $result_loans->fetch_assoc();
    $total_loans = $row_loans['total_loans'];
}

// Query to count all users in the members table
$sql = "SELECT COUNT(*) as total_users FROM members";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the result
    $row = $result->fetch_assoc();
    $total_users = $row['total_users'];
} else {
    echo "0 results";
}

$total_equity = $total_due - $total_payments;


$conn->close();
?>

<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Assets</h3>
        <span class="material-icons-outlined">attach_money</span>
    </div>
    <h1><?php echo number_format($total_due, 2); ?></h1>
</div>

<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Liabilities</h3>
        <span class="material-icons-outlined">money_off</span>
    </div>
    <h1><?php echo number_format($total_payments, 2); ?></h1>
</div>

<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Equity</h3>
        <span class="material-icons-outlined">scale</span>
    </div>
    <h1><?php echo number_format($total_equity, 0); ?></h1>
</div>
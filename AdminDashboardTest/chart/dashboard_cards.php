<?php
include 'connection.php';

// Initialize variables for loan applications
$total_loan_transaction = 0;
$total_approved_loan_transaction = 0;
$total_rejected_loan_transaction = 0;

// Initialize variables for health insurance applications
$total_health_transaction = 0;
$total_approved_health_transaction = 0;
$total_rejected_health_transaction = 0;

$total_loaned_amount = 0;
$total_loaned_paid = 0;

// Query to get the total, approved, and rejected loan applications
$sql = "SELECT 
            COUNT(*) as total_loan_transaction, 
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as total_approved_loan_transaction,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as total_rejected_loan_transaction
        FROM loan_applications";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_loan_transaction = $row['total_loan_transaction'];
    $total_approved_loan_transaction = $row['total_approved_loan_transaction'];
    $total_rejected_loan_transaction = $row['total_rejected_loan_transaction'];
}

// Query to get the total, approved, and rejected health insurance applications
$sql_health = "SELECT 
                COUNT(*) as total_health_transaction, 
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as total_approved_health_transaction,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as total_rejected_health_transaction
            FROM health_insurance_applications";

$result_health = $conn->query($sql_health);

if ($result_health->num_rows > 0) {
    $row_health = $result_health->fetch_assoc();
    $total_health_transaction = $row_health['total_health_transaction'];
    $total_approved_health_transaction = $row_health['total_approved_health_transaction'];
    $total_rejected_health_transaction = $row_health['total_rejected_health_transaction'];
}

// Query to get the sum of loan amounts from loan_applications
$sql_loan_amount = "SELECT SUM(loan_amount) as total_loaned_amount FROM loan_applications WHERE status = 'approved'";

$result_loan_amount = $conn->query($sql_loan_amount);

if ($result_loan_amount->num_rows > 0) {
    $row_loan_amount = $result_loan_amount->fetch_assoc();
    $total_loaned_amount = $row_loan_amount['total_loaned_amount'];
}

// Query to get the sum of payments made from loan_payments
$sql_loan_payments = "SELECT SUM(payment_amount) as total_loaned_paid FROM loan_payments";

$result_loan_payments = $conn->query($sql_loan_payments);

if ($result_loan_payments->num_rows > 0) {
    $row_loan_payments = $result_loan_payments->fetch_assoc();
    $total_loaned_paid = $row_loan_payments['total_loaned_paid'];
}

$sql_total_contributions = "SELECT SUM(contribution_amount) as total_contribution FROM contributions";

$result_total_contributions = $conn->query($sql_total_contributions);

if ($result_total_contributions->num_rows > 0) {
    $row_contribution_payments = $result_total_contributions->fetch_assoc();
    $total_contributions = $row_contribution_payments['total_contribution'];
}

?>
<?php
include 'overall_card.php';
?>

<div class="card" style=" background-color: #fbc02d;">
    <div class="card-inner">
        <h3>Total Health Insurance Applications</h3>
        <span class="material-icons-outlined">credit_card</span>
    </div>
    <h1><?php echo number_format($total_health_transaction, 0); ?></h1>
</div>

<div class="card" style=" background-color: #fbc02d;">
    <div class="card-inner">
        <h3>Approved Health Insurance Applications</h3>
        <span class="material-icons-outlined">check</span>
    </div>
    <h1><?php echo number_format($total_approved_health_transaction, 0); ?></h1>
</div>

<div class="card" style=" background-color: #fbc02d;">
    <div class="card-inner">
        <h3>Rejected Health Insurance Applications</h3>
        <span class="material-icons-outlined">clear</span>
    </div>
    <h1><?php echo number_format($total_rejected_health_transaction, 0); ?></h1>
</div>

<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Total Loan Applications</h3>
        <span class="material-icons-outlined">credit_card</span>
    </div>
    <h1><?php echo number_format($total_loan_transaction, 0); ?></h1>
</div>

<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Approved Loan Applications</h3>
        <span class="material-icons-outlined">check</span>
    </div>
    <h1><?php echo number_format($total_approved_loan_transaction, 0); ?></h1>
</div>

<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Rejected Loan Applications</h3>
        <span class="material-icons-outlined">clear</span>
    </div>
    <h1><?php echo number_format($total_rejected_loan_transaction, 0); ?></h1>
</div>

<div class="card" style="background-color: #fbc02d;">
    <div class="card-inner">
        <h3>Total Loaned Amount</h3>
        <span class="material-icons-outlined">account_balance_wallet</span>
    </div>
    <h1><?php echo number_format($total_loaned_amount, 2); ?></h1>
</div>

<div class="card" style="background-color: #fbc02d;">
    <div class="card-inner">
        <h3>Total Loans Paid</h3>
        <span class="material-icons-outlined">payment</span>
    </div>
    <h1><?php echo number_format($total_loaned_paid, 2); ?></h1>
</div>

<div class="card" style="background-color: #fbc02d;">
    <div class="card-inner">
        <h3>Total Contributions</h3>
        <span class="material-icons-outlined">payment</span>
    </div>
    <h1><?php echo number_format($total_contributions, 2); ?></h1>
</div>
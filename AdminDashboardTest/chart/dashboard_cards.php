<?php
include 'connection.php';

// Initialize the variables for loan applications
$total_loan_transaction = 0;
$total_approved_loan_transaction = 0;
$total_rejected_loan_transaction = 0;

// Initialize the variables for health insurance applications
$total_health_transaction = 0;
$total_approved_health_transaction = 0;
$total_rejected_health_transaction = 0;

// Query to get the total, approved, and rejected loan applications
$sql = "SELECT 
            COUNT(*) as total_loan_transaction, 
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as total_approved_loan_transaction,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as total_rejected_loan_transaction
        FROM loan_applications";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the result
    $row = $result->fetch_assoc();
    $total_loan_transaction = $row['total_loan_transaction'];
    $total_approved_loan_transaction = $row['total_approved_loan_transaction'];
    $total_rejected_loan_transaction = $row['total_rejected_loan_transaction'];
} else {
    echo "0 results for loan applications";
}

// Query to get the total, approved, and rejected health insurance applications
$sql_health = "SELECT 
                COUNT(*) as total_health_transaction, 
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as total_approved_health_transaction,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as total_rejected_health_transaction
            FROM health_insurance_applications";

$result_health = $conn->query($sql_health);

if ($result_health->num_rows > 0) {
    // Fetch the result
    $row_health = $result_health->fetch_assoc();
    $total_health_transaction = $row_health['total_health_transaction'];
    $total_approved_health_transaction = $row_health['total_approved_health_transaction'];
    $total_rejected_health_transaction = $row_health['total_rejected_health_transaction'];
} else {
    echo "0 results for health insurance applications";
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
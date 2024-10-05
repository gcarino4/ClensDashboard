<?php
// Database connection
include 'connection.php';

// Check if the member_id is set in the session
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Member ID is not set in the session.']);
    exit;
}

// Get the current member_id from the session
$member_id = $_SESSION['member_id'];

// Query to total amount_due from receivable table
$sql_amount_due = "SELECT SUM(amount_paid) as total_due FROM receivable";
$result_due = $conn->query($sql_amount_due);

$total_due = 0;
if ($result_due->num_rows > 0) {
    $row_due = $result_due->fetch_assoc();
    $total_due = $row_due['total_due'];
}

// Query to total amount from payments table
$sql_payments = "SELECT SUM(amount) as total_payments FROM payments";
$result_payments = $conn->query($sql_payments);

$total_payments = 0;
if ($result_payments->num_rows > 0) {
    $row_payments = $result_payments->fetch_assoc();
    $total_payments = $row_payments['total_payments'];
}

// Query to total loan_amount for the current member_id from loan_applications table
$sql_loans = "SELECT SUM(loan_amount) as total_loans FROM loan_applications WHERE member_id = ?";
$stmt_loans = $conn->prepare($sql_loans);
$stmt_loans->bind_param("s", $member_id); // Use "s" for string
$stmt_loans->execute();
$result_loans = $stmt_loans->get_result();

$total_loans = 0;
if ($result_loans->num_rows > 0) {
    $row_loans = $result_loans->fetch_assoc();
    $total_loans = $row_loans['total_loans'];
}

// Query to count all users in the members table
$sql_users = "SELECT COUNT(*) as total_users FROM members";
$result_users = $conn->query($sql_users);

$total_users = 0;
if ($result_users->num_rows > 0) {
    $row_users = $result_users->fetch_assoc();
    $total_users = $row_users['total_users'];
} else {
    echo "0 results";
}

// Query to total contribution_amount for the current member_id from contributions table
$sql_contributions = "SELECT SUM(contribution_amount) as total_contributions FROM contributions WHERE member_id = ?";
$stmt_contributions = $conn->prepare($sql_contributions);
$stmt_contributions->bind_param("s", $member_id); // Use "s" for string
$stmt_contributions->execute();
$result_contributions = $stmt_contributions->get_result();

$total_contributions = 0;
if ($result_contributions->num_rows > 0) {
    $row_contributions = $result_contributions->fetch_assoc();
    $total_contributions = $row_contributions['total_contributions'];
}

// Query to total payment_due for the current member_id from health_insurance_applications table
$sql_health_insurance = "SELECT SUM(payment_due) as total_payment_due FROM health_insurance_applications WHERE member_id = ?";
$stmt_health_insurance = $conn->prepare($sql_health_insurance);
$stmt_health_insurance->bind_param("s", $member_id); // Use "s" for string
$stmt_health_insurance->execute();
$result_health_insurance = $stmt_health_insurance->get_result();

$total_payment_due = 0;
if ($result_health_insurance->num_rows > 0) {
    $row_health_insurance = $result_health_insurance->fetch_assoc();
    $total_payment_due = $row_health_insurance['total_payment_due'];
}

// Calculate total equity
$total_equity = $total_due - $total_payments;

$conn->close();
?>

<!-- New Card for Contributions -->
<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Contributions</h3>
        <span class="material-icons-outlined">donate</span> <!-- You can change the icon as needed -->
    </div>
    <h1><?php echo number_format($total_contributions, 2); ?></h1>
</div>

<!-- New Card for Loans -->
<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Total Loans</h3>
        <span class="material-icons-outlined">attach_money</span> <!-- You can change the icon as needed -->
    </div>
    <h1><?php echo number_format($total_loans, 2); ?></h1>
</div>

<!-- New Card for Health Insurance Payment Due -->
<div class="card" style="background-color: #00ff00;">
    <div class="card-inner">
        <h3>Health Insurance Payment Due</h3>
        <span class="material-icons-outlined">payment</span> <!-- You can change the icon as needed -->
    </div>
    <h1><?php echo number_format($total_payment_due, 2); ?></h1>
</div>
<?php
require 'get_member_verified.php';
require 'connection.php'; // Assuming this contains your DB connection logic

$member_id = $_SESSION['member_id']; // Assuming member_id is stored in session
$loan_amount = 0; // Default loan amount, this can be replaced with actual value from a form input or database

// Query to fetch the contribution amount
$query = "SELECT contribution_amount FROM contributions WHERE member_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<div class="reminders">
    <div class="header">
        <h2>Notification</h2>
        <span class="material-icons-sharp">
            notifications_none
        </span>
    </div>

    <?php
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $contribution_amount = $row['contribution_amount'];

        // Determine the loan limit based on contribution amount
        if ($contribution_amount > 10000 && $contribution_amount < 20000) {
            $max_loan = 30000;
        } elseif ($contribution_amount >= 20000 && $contribution_amount < 30000) {
            $max_loan = 60000;
        } elseif ($contribution_amount >= 30000 && $contribution_amount < 40000) {
            $max_loan = 90000;
        } elseif ($contribution_amount >= 40000 && $contribution_amount < 50000) {
            $max_loan = 120000;
        } elseif ($contribution_amount >= 50000 && $contribution_amount < 60000) {
            $max_loan = 150000;
        } elseif ($contribution_amount >= 60000 && $contribution_amount < 70000) {
            $max_loan = 180000;
        } elseif ($contribution_amount >= 70000 && $contribution_amount < 80000) {
            $max_loan = 210000;
        } elseif ($contribution_amount >= 80000 && $contribution_amount < 90000) {
            $max_loan = 240000;
        } elseif ($contribution_amount >= 90000) {
            $max_loan = 270000;
        } else {
            $max_loan = 0; // If contribution is less than or equal to 10,000
        }

        // Displaying the result
        if ($max_loan != 0) {
            echo "
          <div class='notification'>
            <div class='icon'>
                <span class='material-icons-sharp'>
                    info
                </span>
            </div>
            <div class='content'>
                <div class='info'>
                    <h3>You can loan up to: " . number_format($max_loan, 2) . "</h3>
                    <small class='text_muted'>
                       Your contribution amount is: " . htmlspecialchars(number_format($contribution_amount, 2)) . "
                    </small>
                </div>
                <span class='material-icons-sharp'>
                    more_vert
                </span>
            </div>
          </div>";
        }

    } else {
        echo "
          <div class='notification'>
            <div class='icon'>
                <span class='material-icons-sharp'>
                    info
                </span>
            </div>
            <div class='content'>
                <div class='info'>
                    <h3>No Contributions Found</h3>
                </div>
                <span class='material-icons-sharp'>
                    more_vert
                </span>
            </div>
          </div>";
    }
    ?>

    <?php if ($verified == 'True'): ?>
        <div class="notification">
            <div class="icon">
                <span class="material-icons-sharp">
                    check_circle
                </span>
            </div>
            <div class="content">
                <div class="info">
                    <h3>You are now verified!</h3>
                    <small class="text_muted">
                        Your account has been successfully verified.
                    </small>
                </div>
                <span class="material-icons-sharp">
                    more_vert
                </span>
            </div>
        </div>
    <?php endif; ?>

</div> <!-- Close reminders div -->
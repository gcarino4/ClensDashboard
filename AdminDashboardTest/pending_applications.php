<?php
include 'connection.php';

// Ensure session is started and member_id is set
if (!isset($_SESSION['member_id'])) {
    die('Member ID is not set in the session.');
}

// Get the current member_id from the session
$member_id = $_SESSION['member_id'];

// Query to select pending loan applications for the current member_id, including loan_amount
$sqlLoans = "SELECT member_id, application_id, status, loan_amount FROM loan_applications WHERE member_id = ?";
$stmtLoans = $conn->prepare($sqlLoans);
$stmtLoans->bind_param("s", $member_id);
$stmtLoans->execute();
$resultLoans = $stmtLoans->get_result();

// Query to select health insurance applications for the current member_id, including coverage_amount
$sqlHealthInsurance = "SELECT member_id, application_id, status, coverage_amount FROM health_insurance_applications WHERE member_id = ?";
$stmtHealthInsurance = $conn->prepare($sqlHealthInsurance);
$stmtHealthInsurance->bind_param("s", $member_id);
$stmtHealthInsurance->execute();
$resultHealthInsurance = $stmtHealthInsurance->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Applications</title>
</head>

<body>

    <h2>Pending Applications</h2>

    <table>
        <thead>
            <tr>
                <th>Member ID</th>
                <th>Application ID</th>
                <th>Status</th>
                <th>Application Type</th>
                <th>Amount</th> <!-- New column for displaying amount -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Combine results from both loan and health insurance applications
            $hasData = false;

            // Output loan applications
            while ($row = $resultLoans->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["member_id"] . "</td>";
                echo "<td>" . $row["application_id"] . "</td>";
                echo "<td>" . ucfirst($row["status"]) . "</td>";
                echo "<td>Loan Application</td>";
                echo "<td>" . number_format($row["loan_amount"], 2) . "</td>"; // Display loan amount
                echo "</tr>";
                $hasData = true;
            }

            // Output health insurance applications
            while ($row = $resultHealthInsurance->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["member_id"] . "</td>";
                echo "<td>" . $row["application_id"] . "</td>";
                echo "<td>" . ucfirst($row["status"]) . "</td>";
                echo "<td>Health Insurance Application</td>";
                echo "<td>" . number_format($row["coverage_amount"], 2) . "</td>"; // Display coverage amount
                echo "</tr>";
                $hasData = true;
            }

            // If no applications found
            if (!$hasData) {
                echo "<tr><td colspan='5'>No pending applications found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>

</html>

<?php
// Close the statements and the connection
$stmtLoans->close();
$stmtHealthInsurance->close();
$conn->close();
?>
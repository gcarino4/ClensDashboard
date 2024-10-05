<!-- Ledger Table for Receivables -->
<?php

include 'connection.php';

// Initialize $result_receivable to null
$result_receivable = null;

// Get the logged-in user's member_id from the session
$logged_in_member_id = $_SESSION['member_id'];

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Ensure start date is not greater than end date
    if ($start_date <= $end_date) {
        // Query to filter by date range
        $query_receivable = "SELECT * FROM receivable WHERE member_id = '$logged_in_member_id' AND invoice_date BETWEEN '$start_date' AND '$end_date' ORDER BY type, invoice_date";
    }
} else {
    // Default query to display all receivables for the logged-in user
    $query_receivable = "SELECT * FROM receivable WHERE member_id = '$logged_in_member_id' ORDER BY type, invoice_date";
}

// Execute the query
$result_receivable = $conn->query($query_receivable);
?>
<div class="recent-orders" style="border=1">
    <h2 style="text-align: center; margin-bottom: 20px; cursor: pointer;" onclick="toggleCollapse('receivable-matrix')">
        Accounts Paid</h2>
    <div id="receivable-matrix" style="border=1">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border-bottom: 2px solid #ccc; padding: 8px; text-align: left;">Type</th>
                    <th style="border-bottom: 2px solid #ccc; padding: 8px; text-align: left;">Amount</th>
                    <th style="border-bottom: 2px solid #ccc; padding: 8px; text-align: left;">Date</th>
                    <th style="border-bottom: 2px solid #ccc; padding: 8px; text-align: left;">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_type_receivable = null;
                $type_total_receivable = 0;
                $index_receivable = 0; // Index for unique collapsible IDs
                
                // Check if $result_receivable has rows
                if ($result_receivable && $result_receivable->num_rows > 0) {
                    while ($row_receivable = $result_receivable->fetch_assoc()) {
                        if ($current_type_receivable != $row_receivable['type']) {
                            if ($current_type_receivable !== null) {
                                // Display the total for the previous type
                                echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total_receivable, 2) . "</td></tr>";
                            }

                            $current_type_receivable = $row_receivable['type'];
                            $type_total_receivable = 0;
                            $index_receivable++; // Increment index for the next type
                
                            // Group header (clickable)
                            echo "<tr>";
                            echo "<td colspan='4' style='padding: 8px; font-weight: bold; background-color: #f2f2f2; cursor: pointer;' onclick='toggleGroup(\"group-$index_receivable\")'>";
                            echo "<span>" . $current_type_receivable . "</span>";
                            echo "</td>";
                            echo "</tr>";
                        }

                        $type_total_receivable += $row_receivable['amount_due'];

                        // Display each row and assign the same group class for collapsibility
                        echo "<tr class='group-$index_receivable'>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['type'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . number_format($row_receivable['amount_due'], 2) . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['invoice_date'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['note'] . "</td>";
                        echo "</tr>";
                    }
                    // Display the total for the last type
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total_receivable, 2) . "</td></tr>";
                } else {
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: center;'>No receivables found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    include 'loan_payments_table.php';
    echo "<br>";
    include 'health_payments_table.php';
    ?>
</div>

<script>
    // JavaScript function to toggle collapse
    function toggleGroup(groupClass) {
        var rows = document.getElementsByClassName(groupClass);
        for (var i = 0; i < rows.length; i++) {
            if (rows[i].style.display === "none") {
                rows[i].style.display = "table-row";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
</script>
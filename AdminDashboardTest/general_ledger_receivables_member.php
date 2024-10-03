<!-- Ledger Table for Receivables -->
<?php

include 'connection.php';

// Get the logged-in user's member_id from the session
$logged_in_member_id = $_SESSION['member_id'];

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Ensure start date is not greater than end date
    if ($start_date <= $end_date) {
        // Modify the query to include the logged-in user's member_id
        $query_receivable = "SELECT * FROM receivable WHERE member_id = '$logged_in_member_id' AND invoice_date BETWEEN '$start_date' AND '$end_date' ORDER BY type, invoice_date";
        $result_receivable = $conn->query($query_receivable);
    }
}
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
                
                if ($result_receivable->num_rows > 0) {
                    while ($row_receivable = $result_receivable->fetch_assoc()) {
                        if ($current_type_receivable != $row_receivable['type']) {
                            if ($current_type_receivable !== null) {
                                // Display the total for the previous type
                                echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total_receivable, 2) . "</td></tr>";
                                echo "</div>"; // Close the collapsible content
                            }
                            $current_type_receivable = $row_receivable['type'];
                            $type_total_receivable = 0;
                            $index_receivable++; // Increment index for the next type
                
                            echo "<tr>";
                            echo "<td colspan='4' style='padding: 8px; font-weight: bold; background-color: #f2f2f2; cursor: pointer;' onclick='toggleCollapse(\"collapsible-receivable-$index_receivable\")'>";
                            echo "<span>" . $current_type_receivable . "</span>";
                            echo "</td>";
                            echo "</tr>";
                            echo "<div id='collapsible-receivable-$index_receivable' class='collapsible-content' style='display: none;'>"; // Collapsible content starts
                        }

                        $type_total_receivable += $row_receivable['amount_due'];

                        echo "<tr class='collapsible-receivable-$index_receivable' style='display: none;'>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['type'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . number_format($row_receivable['amount_due'], 2) . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['invoice_date'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['note'] . "</td>";
                        echo "</tr>";
                    }
                    // Display the total for the last type
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total_receivable, 2) . "</td></tr>";
                    echo "</div>"; // Close the last collapsible content
                } else {
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: center;'>No receivables found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
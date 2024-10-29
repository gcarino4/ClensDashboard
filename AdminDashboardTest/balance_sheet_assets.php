<?php

include 'connection.php';

$result_receivable = false; // Initialize with a default value
$total_asset = 0; // Initialize total asset

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Ensure start date is not greater than end date
    if ($start_date <= $end_date) {
        $query_receivable = "SELECT * FROM receivable WHERE invoice_date BETWEEN '$start_date' AND '$end_date' ORDER BY type, invoice_date";
        $result_receivable = $conn->query($query_receivable);
    }
}
?>

<div class="recent-orders"
    style="margin: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #f9f9f9;">
    <h2 style="text-align: center; margin-bottom: 20px; cursor: pointer;" onclick="toggleCollapse('receivable-matrix')">
        Assets</h2>
    <div id="receivable-matrix" style="display: none;">
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
                
                // Check if the query returned valid results
                if ($result_receivable && $result_receivable->num_rows > 0) {
                    while ($row_receivable = $result_receivable->fetch_assoc()) {
                        if ($current_type_receivable != $row_receivable['type']) {
                            if ($current_type_receivable !== null) {
                                // Display the total for the previous type
                                echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total_receivable, 2) . "</td></tr>";

                                // Add type total to total asset
                                $total_asset += $type_total_receivable;

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

                        $type_total_receivable += $row_receivable['amount_paid'];

                        echo "<tr class='collapsible-receivable-$index_receivable' style='display: none;'>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['type'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . number_format($row_receivable['amount_paid'], 2) . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['invoice_date'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_receivable['note'] . "</td>";
                        echo "</tr>";
                    }
                    // Display the total for the last type
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total_receivable, 2) . "</td></tr>";

                    // Add last type total to total asset
                    $total_asset += $type_total_receivable;

                    echo "</div>"; // Close the last collapsible content
                } else {
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: center;'>No receivables found</td></tr>";
                }

                // Display the total asset
                echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #d9d9d9;'>Total Asset: " . number_format($total_asset, 2) . "</td></tr>";
                ?>
            </tbody>
        </table>
    </div>
</div>
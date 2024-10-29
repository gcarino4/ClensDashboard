<?php

include 'connection.php';

$result_revenue = false; // Initialize with a default value

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Ensure start date is not greater than end date
    if ($start_date <= $end_date) {
        $query_revenue = "SELECT * FROM revenue WHERE invoice_date BETWEEN '$start_date' AND '$end_date' ORDER BY type, invoice_date";
        $result_revenue = $conn->query($query_revenue);
    }
}
?>

<div class="recent-orders"
    style="margin: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #f9f9f9;">
    <h2 style="text-align: center; margin-bottom: 20px; cursor: pointer;" onclick="toggleCollapse('receivable-matrix')">
        Revenue</h2>
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
                $current_type_revenue = null;
                $total_type_revenue = 0;
                $index_revenue = 0; // Index for unique collapsible IDs
                $total_revenue = 0;

                // Check if the query returned valid results
                if ($result_revenue && $result_revenue->num_rows > 0) {
                    while ($row_revenue = $result_revenue->fetch_assoc()) {
                        if ($current_type_revenue != $row_revenue['type']) {
                            if ($current_type_revenue !== null) {
                                // Display the total for the previous type
                                echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($total_type_revenue, 2) . "</td></tr>";
                                echo "</div>"; // Close the collapsible content
                            }
                            $current_type_revenue = $row_revenue['type'];
                            $total_type_revenue = 0;
                            $index_revenue++; // Increment index for the next type
                
                            echo "<tr>";
                            echo "<td colspan='4' style='padding: 8px; font-weight: bold; background-color: #f2f2f2; cursor: pointer;' onclick='toggleCollapse(\"collapsible-receivable-$index_revenue\")'>";
                            echo "<span>" . $current_type_revenue . "</span>";
                            echo "</td>";
                            echo "</tr>";
                            echo "<div id='collapsible-receivable-$index_revenue' class='collapsible-content' style='display: none;'>"; // Collapsible content starts
                        }

                        $total_type_revenue += $row_revenue['amount_paid'];

                        echo "<tr class='collapsible-receivable-$index_revenue' style='display: none;'>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_revenue['type'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . number_format($row_revenue['amount_paid'], 2) . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_revenue['invoice_date'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row_revenue['note'] . "</td>";
                        echo "</tr>";
                    }

                    // Display the total for the last type
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($total_type_revenue, 2) . "</td></tr>";
                    $total_revenue += $total_type_revenue;

                    echo "</div>"; // Close the last collapsible content
                } else {
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: center;'>No receivables found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
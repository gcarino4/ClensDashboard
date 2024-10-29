<?php
// Assuming you have a database connection established
include "connection.php";

// Check if start_date and end_date are set
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Ensure start date is not greater than end date
    if ($start_date <= $end_date) {
        // Prepare the query
        $query = "SELECT * FROM payments WHERE date BETWEEN '$start_date' AND '$end_date' ORDER BY type, date";
        $result = $conn->query($query);

        // Check if the query execution was successful
        if ($result === false) {
            echo "<p style='color: red; text-align: center;'>Error in SQL query: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Start date cannot be greater than end date.</p>";
    }
} else {
    echo "<p style='color: red; text-align: center;'>Please select a valid date range.</p>";
}
?>

<div class="recent-orders"
    style="margin: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #f9f9f9;">
    <h2 style="text-align: center; margin-bottom: 20px; cursor: pointer;" onclick="toggleCollapse('ledger-matrix')">
        Expenses</h2>
    <div id="ledger-matrix" style="display: none;">
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
                // Only proceed if $result is set and the query was successful
                if (isset($result) && $result->num_rows > 0) {
                    $current_type = null;
                    $type_total = 0;
                    $index = 0; // Index for unique collapsible IDs
                
                    while ($row = $result->fetch_assoc()) {
                        if ($current_type != $row['type']) {
                            if ($current_type !== null) {
                                // Display the total for the previous type
                                echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total, 2) . "</td></tr>";
                                echo "</div>"; // Close the collapsible content
                            }
                            $current_type = $row['type'];
                            $type_total = 0;
                            $index++; // Increment index for the next type
                
                            echo "<tr>";
                            echo "<td colspan='4' style='padding: 8px; font-weight: bold; background-color: #f2f2f2; cursor: pointer;' onclick='toggleCollapse(\"collapsible-$index\")'>";
                            echo "<span>" . $current_type . "</span>";
                            echo "</td>";
                            echo "</tr>";
                            echo "<div id='collapsible-$index' class='collapsible-content' style='display: none;'>"; // Collapsible content starts
                        }

                        $type_total += $row['amount'];

                        echo "<tr class='collapsible-$index' style='display: none;'>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row['type'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . number_format($row['amount'], 2) . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row['date'] . "</td>";
                        echo "<td style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>" . $row['details'] . "</td>";
                        echo "</tr>";
                    }
                    // Display the total for the last type
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: right; font-weight: bold; background-color: #e9e9e9;'>Total: " . number_format($type_total, 2) . "</td></tr>";
                    echo "</div>"; // Close the last collapsible content
                } else {
                    echo "<tr><td colspan='4' style='padding: 8px; text-align: center;'>No payments found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
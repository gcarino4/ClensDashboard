<?php

include 'connection.php';

// Define the SQL query to retrieve all records, including beneficiaries
$sql = "
    SELECT h.*, b.beneficiary_name, b.beneficiary_relationship, b.beneficiary_dob
    FROM health_insurance_applications h
    LEFT JOIN beneficiaries b ON h.application_id = b.application_id
";

// Execute the query and store the results
$result = $conn->query($sql);

// Check if there are any records returned
if ($result->num_rows > 0) {
    // Start the HTML table and output the column headers
    echo "
    <link href='https://fonts.googleapis.com/icon?family=Material+Icons+Sharp' rel='stylesheet'>
    <style>
        .custom-table-container {
            margin-top: 20px;
            overflow-x: auto; /* Makes the container scrollable */
        }
        .custom-table th,
        .custom-table td {
            text-align: center;
            vertical-align: middle; /* Aligns content vertically in the center */
        }
        .custom-table .thead-dark th {
            background-color: #343a40;
            color: #fff;
        }
        .custom-button {
            margin: 0 5px;
        }
        .beneficiary-row {
            display: none; /* Initially hide the beneficiary rows */
        }
    </style>
    <div class='container custom-table-container'>
        <div class='table-responsive'>
            <table class='table table-striped table-bordered custom-table'>
                <thead class='thead-dark'>
                    <tr>
                        <th>Application ID</th>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Birthday</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Insurance Type</th>
                        <th>Coverage Amount</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class='application-data'>";

    $currentApplicationId = null;
    $beneficiaryRows = []; // Array to hold beneficiary rows for current application

    // Loop through the records
    while ($row = $result->fetch_assoc()) {
        // Check if the current row belongs to the same application as the previous one
        if ($currentApplicationId !== $row['application_id']) {
            // If we are switching to a new application, print the previous row if beneficiaries exist
            if ($currentApplicationId !== null) {
                echo "<tr class='application-row' data-toggle='beneficiaries-" . htmlspecialchars($prevRow["application_id"]) . "' style='cursor: pointer;'>
                    <td>" . htmlspecialchars($prevRow["application_id"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["member_id"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["name"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["birthday"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["email"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["phone_number"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["address"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["insurance_type"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["coverage_amount"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["application_date"]) . "</td>
                    <td>" . htmlspecialchars($prevRow["status"]) . "</td>
                    <td>
                        <button class='btn btn-success btn-sm custom-button approveBtn' data-id='" . htmlspecialchars($prevRow["application_id"]) . "'>Approve</button>
                        <button class='btn btn-danger btn-sm custom-button rejectBtn' data-id='" . htmlspecialchars($prevRow["application_id"]) . "'>Reject</button>
                    </td>
                </tr>";

                // Print the beneficiary rows
                foreach ($beneficiaryRows as $beneficiary) {
                    echo "<tr class='beneficiary-row beneficiaries-" . htmlspecialchars($prevRow["application_id"]) . "'>
                        <td colspan='11' style='text-align:left;'>
                            <strong>Beneficiary Name:</strong> " . htmlspecialchars($beneficiary['name']) . " <br>
                            <strong>Relationship:</strong> " . htmlspecialchars($beneficiary['relationship']) . " <br>
                            <strong>DOB:</strong> " . htmlspecialchars($beneficiary['dob']) . "
                        </td>
                    </tr>";
                }
            }

            // Reset for the new application
            $beneficiaryRows = [];
            $currentApplicationId = $row['application_id'];
        }

        // Add the current row's beneficiary to the list if it exists
        if (!empty($row["beneficiary_name"])) {
            $beneficiaryRows[] = [
                'name' => $row["beneficiary_name"],
                'relationship' => $row["beneficiary_relationship"],
                'dob' => $row["beneficiary_dob"]
            ];
        }

        // Save the current row as the previous row for the next loop iteration
        $prevRow = $row;
    }

    // Output the last application row after the loop ends
    if ($currentApplicationId !== null) {
        echo "<tr class='application-row' data-toggle='beneficiaries-" . htmlspecialchars($prevRow["application_id"]) . "' style='cursor: pointer;'>
            <td>" . htmlspecialchars($prevRow["application_id"]) . "</td>
            <td>" . htmlspecialchars($prevRow["member_id"]) . "</td>
            <td>" . htmlspecialchars($prevRow["name"]) . "</td>
            <td>" . htmlspecialchars($prevRow["birthday"]) . "</td>
            <td>" . htmlspecialchars($prevRow["email"]) . "</td>
            <td>" . htmlspecialchars($prevRow["phone_number"]) . "</td>
            <td>" . htmlspecialchars($prevRow["address"]) . "</td>
            <td>" . htmlspecialchars($prevRow["insurance_type"]) . "</td>
            <td>" . htmlspecialchars($prevRow["coverage_amount"]) . "</td>
            <td>" . htmlspecialchars($prevRow["application_date"]) . "</td>
            <td>" . htmlspecialchars($prevRow["status"]) . "</td>
            <td>
                <button class='btn btn-success btn-sm custom-button approveBtn' data-id='" . htmlspecialchars($prevRow["application_id"]) . "'>Approve</button>
                <button class='btn btn-danger btn-sm custom-button rejectBtn' data-id='" . htmlspecialchars($prevRow["application_id"]) . "'>Reject</button>
            </td>
        </tr>";

        // Print the beneficiary rows
        foreach ($beneficiaryRows as $beneficiary) {
            echo "<tr class='beneficiary-row beneficiaries-" . htmlspecialchars($prevRow["application_id"]) . "'>
                <td colspan='11' style='text-align:left;'>
                    <strong>Beneficiary Name:</strong> " . htmlspecialchars($beneficiary['name']) . " <br>
                    <strong>Relationship:</strong> " . htmlspecialchars($beneficiary['relationship']) . " <br>
                    <strong>DOB:</strong> " . htmlspecialchars($beneficiary['dob']) . "
                </td>
            </tr>";
        }
    }

    echo "</tbody>
        </table>
    </div>
    <script>
        // JavaScript to toggle the visibility of beneficiary rows
        document.querySelectorAll('.application-row').forEach(row => {
            row.addEventListener('click', () => {
                // Toggle the associated beneficiary rows
                const applicationId = row.getAttribute('data-toggle').split('-')[1]; // Extract application ID
                const beneficiaryRows = document.querySelectorAll('.beneficiary-row.beneficiaries-' + applicationId);
                beneficiaryRows.forEach(benRow => {
                    // Toggle display
                    benRow.style.display = (benRow.style.display === 'none' || benRow.style.display === '') ? 'table-row' : 'none';
                });
            });
        });
    </script>
</div>";

} else {
    // No records found
    echo "<div class='container custom-table-container'>
            <p class='text-center'>No results found</p>
          </div>";
}

// Close the database connection
$conn->close();
?>
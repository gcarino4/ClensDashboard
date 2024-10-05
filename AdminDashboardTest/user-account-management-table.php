<?php

include 'connection.php';

// Define the SQL query to retrieve all records
$sql = "SELECT * FROM members";

// Execute the query and store the results
$result = $conn->query($sql);

// Check if there are any records returned
if ($result->num_rows > 0) {
    // Start the HTML table and output the column headers
    echo "<div class='table-container'>
    <table>
      <tr>
        <th>Member ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Birthday</th>
        <th>Sex</th>
        <th>Civil Status</th>
        <th>Address</th>
        <th>Contact Number</th>
        <th>Role</th>
        <th>Verification</th>
        <th>Action</th>
      </tr>";

    // Loop through the results and display the table rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
      <td>" . htmlspecialchars($row["member_id"]) . "</td>
      <td>" . htmlspecialchars($row["name"]) . "</td>
      <td>" . htmlspecialchars($row["age"]) . "</td>
      <td>" . htmlspecialchars($row["birthday"]) . "</td>
      <td>" . htmlspecialchars($row["sex"]) . "</td>
      <td>" . htmlspecialchars($row["civil_status"]) . "</td>
      <td>" . htmlspecialchars($row["address"]) . "</td>
      <td>" . htmlspecialchars($row["contact_no"]) . "</td>
      <td>" . htmlspecialchars($row["role"]) . "</td>
      <td>" . htmlspecialchars($row["verified"]) . "</td>";

        // Action buttons
        echo "<td>
        <button class='editBtn' data-id='" . htmlspecialchars($row["id"]) . "'>✏️</button>
        <button class='archiveBtn' data-id='" . htmlspecialchars($row["id"]) . "'>📁</button>
      </td>
    </tr>";
    }
    echo "</table></div>";

} else {
    // No records found
    echo "0 results";
}

// Close the database connection
$conn->close();
?>
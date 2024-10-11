<?php

namespace user_account_table;


include 'connection.php';

// Define the SQL query to retrieve all records from members
// and join with the contributions table to get contribution_amount
$sql = "
    SELECT m.*, m.date_of_creation, COALESCE(c.contribution_amount, 0) AS contribution_amount
    FROM members m
    LEFT JOIN contributions c ON m.member_id = c.member_id
";


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
        <th>Contribution Amount</th>
        <th>Date of Creation</th>";

  // Check if the session role is Admin or Admin Officer to show the Action column
  if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Admin Officer')) {
    echo "<th>Action</th>";
  }

  echo "</tr>";

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
      <td>" . htmlspecialchars($row["verified"]) . "</td>
      <td>" . htmlspecialchars($row["contribution_amount"]) . "</td>
      <td>" . htmlspecialchars($row["date_of_creation"]) . "</td>"; // Add this line



    // Conditionally display the action buttons based on the session role
    if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Admin Officer')) {
      echo "<td>
              <button class='editBtn' data-id='" . htmlspecialchars($row["member_id"]) . "'>✏️</button>
              <button class='archiveBtn' data-id='" . htmlspecialchars($row["member_id"]) . "'>📁</button>
            </td>";
    }

    echo "</tr>";
  }
  echo "</table></div>";

} else {
  // No records found
  echo "0 results";
}

// Close the database connection
$conn->close();
?>
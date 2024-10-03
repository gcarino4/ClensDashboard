<?php

include 'connection.php';

// Define the SQL query to retrieve all records
$sql = "SELECT * FROM loan_applications";

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
            overflow-x: auto;
             overflow-y: auto; /* Makes the container scrollable */
            width: 900px;
            height: 400px;
        }
            
        .custom-table th {
    position: sticky;
    top: 0; /* Position the sticky header at the top */
    background-color: white; /* Background color to cover content below */
    z-index: 1; /* Ensure it stays above other content */
}
    
        .custom-table th,
        .custom-table td {
            overflow: auto;
            text-align: center;
            vertical-align: middle; /* Aligns content vertically in the center */
            padding: 15px;
        }
        
        

        .custom-button-reject {
            margin: 5px 5px;
            background-color: red;
            padding: 5px;
            color: white;
        }
            .custom-button-approve {
            margin: 5px 5px;
            background-color: green;
            padding: 5px;
            color: white;
        }

        .img-preview {
            width: 100px; /* Adjust the size as needed */
            height: auto;
            cursor: pointer; /* Change cursor to pointer for clickable images */
        }
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; 
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.9); 
            padding-top: 60px; 
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <div class='container custom-table-container'>
        <div class='table-responsive'>
            <table class='table table-striped table-bordered custom-table'>
                <thead class='thead-sticky'>
                    <tr>
                        <th>Application ID</th>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Annual Income</th>
                        <th>Loan Amount</th>
                        <th>Payment Plan</th>
                        <th>Loan Term</th>
                        <th>Loan Purpose</th>
                        <th>Collateral</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Supporting Document 1</th>
                        <th>Supporting Document 2</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class='application-data'>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row["application_id"]) . "</td>
            <td>" . htmlspecialchars($row["member_id"]) . "</td>
            <td>" . htmlspecialchars($row["name"]) . "</td>
            <td>" . htmlspecialchars($row["email"]) . "</td>
            <td>" . htmlspecialchars($row["phone_number"]) . "</td>
            <td>" . htmlspecialchars($row["address"]) . "</td>
            <td>" . htmlspecialchars($row["annual_income"]) . "</td>
            <td>" . htmlspecialchars($row["loan_amount"]) . "</td>
            <td>" . htmlspecialchars($row["payment_plan"]) . "</td>
            <td>" . htmlspecialchars($row["loan_term"]) . "</td>
            <td>" . htmlspecialchars($row["loan_purpose"]) . "</td>
            <td>" . htmlspecialchars($row["collateral"]) . "</td>
            <td>" . htmlspecialchars($row["application_date"]) . "</td>
            <td>" . htmlspecialchars($row["status"]) . "</td>
            <td>
                <img src='data:image/jpeg;base64," . htmlspecialchars($row["supporting_document_1"]) . "' class='img-preview' alt='Supporting Document 1' onclick='openModal(this.src)'/>
            </td>
            <td>
                <img src='data:image/jpeg;base64," . htmlspecialchars($row["supporting_document_2"]) . "' class='img-preview' alt='Supporting Document 2' onclick='openModal(this.src)'/>
            </td>
            <td>
                <button class='custom-button-approve approveBtn' data-id='" . htmlspecialchars($row["application_id"]) . "'>
    <i class='fas fa-check-square'></i> Approve
</button>
<button class='custom-button-reject rejectBtn' data-id='" . htmlspecialchars($row["application_id"]) . "'>
    <i class='fas fa-ban'></i> Reject
</button>

            </td>
        </tr>";
    }

    echo "</tbody>
        </table>
    </div>
    <script src='loan_application_table.js'></script>
    </div>

    <!-- Modal for displaying enlarged images -->
    <div id='imageModal' class='modal'>
      <span class='close' onclick='closeModal()'>&times;</span>
      <img class='modal-content' id='imgModal'>
      <div id='caption'></div>
    </div>

    <script>
        function openModal(src) {
            var modal = document.getElementById('imageModal');
            var modalImg = document.getElementById('imgModal');
            modal.style.display = 'block';
            modalImg.src = src;
        }

        function closeModal() {
            var modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }
    </script>
    ";

} else {
    // No records found
    echo "<div class='container custom-table-container'>
            <p class='text-center'>No results found</p>
          </div>";
}

// Close the database connection
$conn->close();
?>
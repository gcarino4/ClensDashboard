<?php

include 'connection.php';

// Define the SQL query to retrieve all records, including the valid_id column
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
        <th>Valid ID</th> <!-- New column for valid ID -->
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

        // Display the valid ID as an image by decoding Base64 with dynamic MIME type
        if (!empty($row["valid_id"])) {
            $valid_id = $row['valid_id'];

            if (strpos($valid_id, '/') !== false) {
                echo "<td><img src='" . htmlspecialchars($valid_id) . "' alt='Valid ID' class='valid-id-img' style='width:100px;height:auto;' onclick='enlargeImage(this)'></td>";
            } elseif (substr($valid_id, 0, 5) == '/9j/4') {
                echo "<td><img src='data:image/jpeg;base64," . htmlspecialchars($valid_id) . "' alt='Valid ID' class='valid-id-img' style='width:100px;height:auto;' onclick='enlargeImage(this)'></td>";
            } elseif (substr($valid_id, 0, 8) == 'iVBORw0K') {
                echo "<td><img src='data:image/png;base64," . htmlspecialchars($valid_id) . "' alt='Valid ID' class='valid-id-img' style='width:100px;height:auto;' onclick='enlargeImage(this)'></td>";
            } else {
                echo "<td>Invalid Image Format</td>";
            }
        } else {
            echo "<td>No ID</td>";
        }

        // Action buttons
        echo "<td>
        <button class='editBtn' data-id='" . htmlspecialchars($row["id"]) . "'>✏️</button>
        <button class='archiveBtn' data-id='" . htmlspecialchars($row["id"]) . "'>📁</button>
      </td>
    </tr>";
    }
    echo "</table></div>";

    // Modal for displaying the enlarged image
    echo "
    <div id='imageModal' class='modal'>
      <span class='close' onclick='closeModal()'>&times;</span>
      <img class='modal-content' id='imgModal'>
      <div id='caption'></div>
    </div>";

} else {
    // No records found
    echo "0 results";
}

// Close the database connection
$conn->close();
?>

<!-- Add CSS for modal styling -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        padding-top: 100px;
        left: 70;
        top: 50;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 500px;
    }

    .modal-content,
    .close {
        animation: zoom 0.6s;
    }

    @keyframes zoom {
        from {
            transform: scale(0)
        }

        to {
            transform: scale(1)
        }
    }

    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #fff;
        font-size: 35px;
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

<!-- Add JavaScript to handle modal opening and closing -->
<script>
    function enlargeImage(img) {
        var modal = document.getElementById('imageModal');
        var modalImg = document.getElementById('imgModal');
        var caption = document.getElementById('caption');

        modal.style.display = 'block';
        modalImg.src = img.src;
        caption.innerHTML = img.alt;
    }

    function closeModal() {
        var modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }
</script>
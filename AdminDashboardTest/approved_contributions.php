<?php
namespace approved_contributions;

include 'connection.php';


// Get the member_id from the session
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Member ID is not set in the session.']);
    exit;
}

$member_id = $_SESSION['member_id'];

// Query to fetch contributions data only for the current session member_id
$sql = "SELECT * FROM contributions WHERE member_id = '$member_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

    <table>
        <tr>
            <th>Contribution ID</th>
            <th>Member ID</th>
            <th>Contribution Amount</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr onclick='opencontributionPaymentForm(\"" . $row["contribution_id"] . "\")'>";
                echo "<td>" . $row["contribution_id"] . "</td>";
                echo "<td>" . $row["member_id"] . "</td>";
                echo "<td id='amount_" . $row["contribution_id"] . "'>" . $row["contribution_amount"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No records found</td></tr>";
        }
        ?>
    </table>

    <!-- Payment Modal -->
    <div id="contributionPaymentModal" class="modal">
        <div class="modal-content">
            <h2>Process Contribution Payment
                <span class="close" onclick="closeContributionModal()">&times;</span>
            </h2>
            <br>
            <form id="contributionPaymentForm" enctype="multipart/form-data">
                <input type="text" name="contribution_id" id="contribution_id" readonly>
                <label for="payment_amount">Payment Amount:</label>
                <input type="number" name="payment_amount" id="payment_amount" required>
                <label for="payment_image">Upload Image:</label>
                <input type="file" name="payment_image" id="payment_image" accept="image/*">
                <button type="submit">Submit Payment</button>
            </form>
        </div>
    </div>

    <script>
        // Function to open the payment modal
        function opencontributionPaymentForm(contribution_id) {
            document.getElementById('contribution_id').value = contribution_id;
            document.getElementById('contributionPaymentModal').style.display = 'block';
        }

        // Function to close the modal
        function closeContributionModal() {
            document.getElementById('contributionPaymentModal').style.display = 'none';
        }

        // Handle form submission with AJAX
        document.getElementById('contributionPaymentForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);

            // Read the image file and convert it to Base64
            const fileInput = document.getElementById('payment_image');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onloadend = function () {
                    formData.append('payment_image_base64', reader.result); // Append Base64 image data

                    fetch('contributions_process_payment.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data); // Check the response from the server
                            if (data.success) {
                                document.getElementById('amount_' + data.contribution_id).innerText = data.new_amount;
                                closeContributionModal(); // Close the modal
                            } else {
                                alert('Payment failed: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                };
                reader.readAsDataURL(file); // Convert the file to Base64
            } else {
                // Handle case where no file is selected
                fetch('contributions_process_payment.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data); // Check the response from the server
                        if (data.success) {
                            document.getElementById('amount_' + data.contribution_id).innerText = data.new_amount;
                            closeContributionModal(); // Close the modal
                        } else {
                            alert('Payment failed: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // Close the modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('contributionPaymentModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>


</body>

</html>

<?php
$conn->close();
?>
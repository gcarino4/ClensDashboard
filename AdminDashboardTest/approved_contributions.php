<?php
namespace approved_contributions;

include 'connection.php';

// Query to fetch all contributions data
$sql = "SELECT * FROM contributions";
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
            <th>Action</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr onclick='opencontributionPaymentForm(\"" . $row["contribution_id"] . "\", \"" . $row["member_id"] . "\")'>";
                echo "<td>" . $row["contribution_id"] . "</td>";
                echo "<td>" . $row["member_id"] . "</td>";
                echo "<td id='amount_" . $row["contribution_id"] . "'>" . $row["contribution_amount"] . "</td>";
                echo "<td><button type='button' onClick='opencontributionPaymentForm(\"" . $row["contribution_id"] . "\", \"" . $row["member_id"] . "\")'>Make Payment</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found</td></tr>";
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
                <h4>Contribution amount must not be less than 1500 PHP or more than 5000 PHP</h4>
                <br>

                <input type="text" name="contribution_id" id="contribution_id" readonly>
                <input type="text" name="member_id" id="member_id" readonly>
                <label for="payment_amount">Payment Amount:</label>
                <input type="number" name="payment_amount" id="payment_amount" required>
                <label for="payment_image">Upload Image:</label>
                <input type="file" name="payment_image" id="payment_image" accept="image/*" required>
                <button type="submit">Submit Payment</button>
            </form>
        </div>
    </div>

    <script>
        // Function to open the payment modal
        function opencontributionPaymentForm(contribution_id, member_id) {
            document.getElementById('contribution_id').value = contribution_id;
            document.getElementById('member_id').value = member_id;

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
                        .then(response => response.json())  // Parse JSON response
                        .then(data => {
                            console.log('Response:', data);  // Debugging: Log response

                            if (data.success) {
                                // Update the contribution amount
                                document.getElementById('amount_' + data.contribution_id).innerText = data.new_amount;

                                // Show success alert
                                alert(data.message);

                                // Close the modal
                                closeContributionModal();
                                location.reload(true);
                            } else {
                                // Show error alert
                                alert('Payment failed: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error); // Debugging: Log fetch errors
                            alert('An error occurred while processing the payment.');
                        });
                };
                reader.readAsDataURL(file); // Convert the file to Base64
            } else {
                // Handle case where no file is selected
                fetch('contributions_process_payment.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())  // Parse JSON response
                    .then(data => {
                        console.log('Response:', data);  // Debugging: Log response

                        if (data.success) {
                            // Update the contribution amount
                            document.getElementById('amount_' + data.contribution_id).innerText = data.new_amount;

                            // Show success alert
                            alert(data.message);

                            // Close the modal
                            closeContributionModal();
                        } else {
                            // Show error alert
                            alert('Payment failed: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error); // Debugging: Log fetch errors
                        alert('An error occurred while processing the payment.');
                    });
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
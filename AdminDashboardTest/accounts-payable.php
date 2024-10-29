<?php
include 'connection.php';
include 'check_user.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = htmlspecialchars($_POST['type']);
    $amount = htmlspecialchars($_POST['amount']);
    $date = htmlspecialchars($_POST['date']);
    $details = htmlspecialchars($_POST['details']);

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO payments (type, amount, date, details) VALUES (?, ?, ?, ?)");
    $bind = $stmt->bind_param("sdss", $type, $amount, $date, $details);
    if ($bind === false) {
        die("Bind param failed: " . $stmt->error);
    }
    if ($stmt->execute()) {
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <!-- Montserrat Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/cards.css">
    <title>CoLens Dashboard Design</title>
</head>

<body>

    <div class="container">

        <?php
        include 'sidebar.php';
        ?>

        <!-- Main Content -->
        <main>
            <h1>Accounts Payable</h1>
            <!-- Analyses -->
            <div class="analyse">
                <?php
                include 'chart/overall_card.php';
                ?>
            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->

            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">
                <h2>Recent Payments</h2>
                <button id="openModalBtn">Add Payments</button>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <form id="apForm" action="" method="POST" onsubmit="return validateForm()">
                            <label for="type">Type</label>
                            <input type="text" id="type" name="type" required>

                            <label for="amount">Amount</label>
                            <input type="number" id="amount" name="amount" step="0.01" required>

                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" required>

                            <label for="details">Details</label>
                            <textarea id="details" name="details" rows="4" required></textarea>

                            <button type="submit">Submit</button>
                        </form>
                        <script>
                            // Get the modal
                            var modal = document.getElementById("myModal");

                            // Get the button that opens the modal
                            var btn = document.getElementById("openModalBtn");

                            // Get the <span> element that closes the modal
                            var span = document.getElementsByClassName("close")[0];

                            // When the user clicks the button, open the modal 
                            btn.onclick = function () {
                                modal.style.display = "block";
                            }

                            // When the user clicks on <span> (x), close the modal
                            span.onclick = function () {
                                modal.style.display = "none";
                            }

                            // When the user clicks anywhere outside of the modal, close it
                            window.onclick = function (event) {
                                if (event.target == modal) {
                                    modal.style.display = "none";
                                }
                            }

                            function validateForm() {
                                const type = document.getElementById('type').value;
                                const amount = document.getElementById('amount').value;
                                const date = document.getElementById('date').value;
                                const details = document.getElementById('details').value;

                                if (!type || !amount || !date || !details) {
                                    alert('All fields are required.');
                                    return false;
                                }

                                if (amount <= 0) {
                                    alert('Amount must be a positive number.');
                                    return false;
                                }

                                return true;
                            }
                        </script>

                    </div>
                </div>

                <?php

                include 'connection.php';

                // Define the SQL query to retrieve all records
                $sql = "SELECT * FROM payments";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {  // Check if there are more than 0 rows
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Type</th><th>Amount</th><th>Date</th><th>Details</th></tr>";

                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["type"] . "</td>";
                        echo "<td>" . $row["amount"] . "</td>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["details"] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "No results found";
                }

                $conn->close();
                ?>

            </div>
            <!-- End of Recent Orders -->

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <?php
                include 'profile.php';
                ?>

            </div>
            <!-- End of Nav -->

            <div class="user-profile">
                <div class="logo">
                    <img src="images/CoLens.png">
                    <h2>Accounting Management System</h2>
                    <p>GSCTEMPCO</p>
                </div>
            </div>

            <?php
            include 'verified_notification.php';
            ?>

        </div>


    </div>
    <script src="form.js"></script>


</body>

</html>
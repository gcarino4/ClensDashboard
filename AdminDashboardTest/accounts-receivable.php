<?php
include 'connection.php'; // Assuming this file contains your database connection details
include 'check_user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoice_date = $_POST['invoice_date'];
    $amount_paid = $_POST['amount_paid'];
    $note = $_POST['note'];
    $type = $_POST['type'];
    $invoiced_by = $_SESSION['name'];

    if (!empty($invoice_date)) {
        $sql = "INSERT INTO receivable (invoiced_by, invoice_date, amount_paid, note, type)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $invoiced_by, $invoice_date, $amount_paid, $note, $type);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: Invoice date cannot be empty.";
    }
}

$conn->close();
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
            <h1>Accounts Receivable</h1>

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
                <h2>Recent Payments By Members</h2>
                <!-- New Payment Receivable Section -->
                <button id="openReceivableModalBtn">Add Payment Receivable</button>

                <div id="receivableModal" class="modal">
                    <div class="modal-content">

                        <form id="receivableForm" action="" method="POST">
                            <!-- Removed member_id and member_name fields -->

                            <label for="invoice_date">Invoice Date</label>
                            <input type="date" id="invoice_date" name="invoice_date"
                                value="<?php echo date('Y-m-d'); ?>" required>

                            <label for="amount_paid">Amount Paid</label>
                            <input type="number" id="amount_paid" name="amount_paid" step="0.01" required>

                            <label for="note">Note</label>
                            <textarea id="note" name="note" rows="4"></textarea>

                            <label for="type">Type</label>
                            <input id="type" name="type" required>

                            <button type="submit" name="receivable_submit">Submit</button>
                        </form>

                    </div>
                </div>


                <?php
                include 'connection.php';


                $sql = "SELECT * FROM receivable";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Error in SQL query: " . $conn->error); // Check for SQL errors
                }

                if ($result->num_rows > 0) {
                    echo '<div class="table-responsive">'; // Bootstrap responsive table
                    echo '<table class="table table-striped table-bordered">'; // Bootstrap classes for styling
                
                    // Table headers
                    echo '<thead class="thead-dark">'; // Dark header background
                    echo '<tr>';
                    echo '<th>Invoiced By</th>';
                    echo '<th>TRX CODE</th>';
                    echo '<th>Invoice Date</th>';
                    echo '<th>Amount Paid</th>';
                    echo '<th>Note</th>';
                    echo '<th>Type</th>';
                    echo '</tr>';
                    echo '</thead>';

                    echo '<tbody>'; // Table body start
                
                    // Loop through the results
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row["invoiced_by"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["transaction_code"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["invoice_date"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["amount_paid"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["note"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["type"]) . '</td>';

                        echo '</tr>';
                    }

                    echo '</tbody>'; // Table body end
                    echo '</table>';
                    echo '</div>'; // End of table-responsive
                } else {
                    echo "<p>No results found.</p>";
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




    <script>
        // Get the button that opens the receivable modal
        var receivableBtn = document.getElementById("openReceivableModalBtn");

        // Get the modal
        var receivableModal = document.getElementById("receivableModal");

        // When the user clicks the button, open the modal
        receivableBtn.onclick = function () {
            receivableModal.style.display = "block";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == receivableModal) {
                receivableModal.style.display = "none";
            }
        }

    </script>

</body>

</html>
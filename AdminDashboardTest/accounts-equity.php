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
        $sql = "INSERT INTO equity (invoiced_by, invoice_date, amount_paid, note, type)
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
            <h1>Income Statement</h1>

            <!-- Analyses -->
            <div class="analyse">
                <?php
                include 'chart/overall_card.php';
                ?>
            </div>

            <div class="recent-orders">
                <h2>Recent Equity Contributions By Members</h2>
                <!-- New Equity Contribution Section -->
                <button id="openEquityModalBtn">Add Equity Contribution</button>

                <div id="equityModal" class="modal">
                    <div class="modal-content">

                        <form id="equityForm" action="" method="POST">
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

                            <button type="submit" name="equity_submit">Submit</button>
                        </form>

                    </div>
                </div>

                <?php
                include 'connection.php';

                $sql = "SELECT * FROM equity";
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
    </div>


    </div>

    <script>
        // Get the button that opens the equity modal
        var equityBtn = document.getElementById("openEquityModalBtn");

        // Get the modal
        var equityModal = document.getElementById("equityModal");

        // When the user clicks the button, open the modal
        equityBtn.onclick = function () {
            equityModal.style.display = "block";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == equityModal) {
                equityModal.style.display = "none";
            }
        }
    </script>

</body>

</html>
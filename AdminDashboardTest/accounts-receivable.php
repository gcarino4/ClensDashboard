<?php
include 'connection.php'; // Assuming this file contains your database connection details
include 'check_user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $member_name = $_POST['member_name'];
    $invoice_date = $_POST['invoice_date'];
    $due_date = $_POST['due_date'];
    $amount_due = $_POST['amount_due'];
    $amount_paid = $_POST['amount_paid']; // Capture amount paid from form
    $payment_status = $_POST['payment_status']; // Capture payment status from form
    $note = $_POST['note'];
    $type = $_POST['type'];
    $invoiced_by = $_SESSION['name'];

    if (!empty($invoice_date) && !empty($due_date)) {
        $sql = "INSERT INTO receivable (member_id, invoiced_by, member_name, invoice_date, due_date, amount_due, amount_paid, payment_status, note, type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssdsss", $member_id, $invoiced_by, $member_name, $invoice_date, $due_date, $amount_due, $amount_paid, $payment_status, $note, $type);


        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: Invoice date or due date cannot be empty.";
    }
}




// Close connection
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
                <button id="openModalBtn">Add Payment Receivable</button>

                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <form id="apForm" action="" method="POST" onsubmit="return validateForm()">

                            <label for="member_name">Invoiced By</label>
                            <input id="invoiced_by" name="invoiced_by" value="<?php echo $_SESSION['name']; ?>" required
                                readonly>

                            <label for="member_id">Member ID</label>
                            <input type="text" id="member_id" name="member_id" required oninput="fetchMemberName()">

                            <label for="member_name">Member Name</label>
                            <input id="member_name" name="member_name" required readonly>

                            <label for="invoice_date">Invoice Date</label>
                            <input type="date" id="invoice_date" name="invoice_date"
                                value="<?php echo date('Y-m-d'); ?>" readonly>

                            <label for="due_date">Due Date</label>
                            <input type="date" id="due_date" name="due_date" required>

                            <label for="amount_due">Amount Due</label>
                            <input type="number" id="amount_due" name="amount_due" step="0.01" required>


                            <label for="amount_paid">Amount Paid</label>
                            <input type="number" id="amount_paid" name="amount_paid" step="0.01" required>

                            <label for="payment_status_select">Payment Status</label>
                            <select id="payment_status_select" name="payment_status" required>
                                <option value="Ongoing" selected>Ongoing</option>
                                <option value="Paid">Paid</option>
                                <option value="Overdue">Overdue</option>
                                <option value="Due">Due</option>
                            </select>


                            <label for="note">Note</label>
                            <textarea id="note" name="note" rows="4"></textarea>

                            <label for="type">Type</label>
                            <input id="type" name="type"></input>

                            <button type="submit">Submit</button>
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
                    echo '<th>Member Name</th>';
                    echo '<th>Invoice Date</th>';
                    echo '<th>Due Date</th>';
                    echo '<th>Amount Due</th>';
                    echo '<th>Amount Paid</th>';
                    echo '<th>Payment Status</th>';
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
                        echo '<td>' . htmlspecialchars($row["member_name"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["invoice_date"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["due_date"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["amount_due"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["amount_paid"]) . '</td>';
                        echo '<td>' . htmlspecialchars($row["payment_status"]) . '</td>';
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

        function fetchMemberName() {
            const memberId = document.getElementById('member_id').value;

            if (memberId) {
                // Send AJAX request to get the member name
                fetch('get_member_name.php?member_id=' + memberId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('member_name').value = data.member_name;
                        } else {
                            document.getElementById('member_name').value = ''; // Clear if not found
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                document.getElementById('member_name').value = ''; // Clear if empty input
            }
        }

        function validateForm() {
            const member_name = document.getElementById('member_name').value;
            const invoice_date = document.getElementById('invoice_date').value;
            const due_date = document.getElementById('due_date').value;
            const amount_due = document.getElementById('amount_due').value;
            const amount_paid = document.getElementById('amount_paid').value;
            const payment_status = document.getElementById('payment_status').value;
            const note = document.getElementById('note').value;
            const type = document.getElementById('type').value;

            if (!member_name || !invoice_date || !due_date || !amount_due || !amount_paid || !payment_status) {
                alert('All fields are required.');
                return false;
            }

            if (amount_due <= 0 || amount_paid < 0) {
                alert('Amount Due must be a positive number and Amount Paid cannot be negative.');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>
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
            <h1>Add Contribution</h1>

            <!-- Analyses -->
            <div class="analyse">

            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->

            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">

                <?php
                echo " <h2>Member Contributions</h2>";
                include 'approved_contributions.php';
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

</body>

</html>
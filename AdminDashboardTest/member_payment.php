<?php
require 'check_user.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/cards.css">
    <link rel="stylesheet" href="css/application.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="index.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>CoLens Dashboard</title>
    <script>
        // Set the isVerified variable based on the PHP session
        const isVerified = <?php echo json_encode($_SESSION['verified'] === 'True'); ?>;
    </script>
</head>

<body>

    <div class="container" style="overflow-y: auto">
        <?php include "sidebar.php"; ?>

        <!-- Main Content -->
        <main>
            <h1>Welcome, <?php echo htmlspecialchars($displayName); ?></h1>

            <!-- Analyses -->
            <div class="analyse">
                <!-- Your analyses content -->
                <?php
                include './chart/payment_cards.php';
                ?>
            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="new-users">

                <!-- Your new users content -->
            </div>
            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">



                <?php
                echo " <h2>Contributions To Pay</h2>";
                include 'approved_contributions.php';
                echo "<br>";
                echo " <h2>Loans To Pay</h2>";
                include 'approved_loans.php';
                echo "<br>";
                echo " <h2>Insurance Premium To Pay</h2>";
                include 'approved_health_insurance.php';


                ?>
                <!-- Style for the Modal (optional) -->
                <style>
                    .modal {
                        display: none;
                        position: fixed;
                        z-index: 1;
                        padding-top: 60px;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgb(0, 0, 0);
                        background-color: rgba(0, 0, 0, 0.4);
                    }

                    .modal-content {
                        background-color: #fefefe;
                        margin: 5% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                    }

                    .close {
                        color: #aaa;
                        float: right;
                        font-size: 28px;
                        font-weight: bold;
                    }

                    .close:hover,
                    .close:focus {
                        color: black;
                        text-decoration: none;
                        cursor: pointer;
                    }
                </style>




            </div>

            <!-- End of Recent Orders Table -->
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
<?php

require 'check_user.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/user_account_management.css">
    <script src="index.js"></script>
    <title>CoLens Dashboard</title>
</head>

<body>

    <div class="container">
        <?php
        include "sidebar.php"
            ?>

        <!-- Main Content -->
        <main>
            <h1>Applications</h1>
            <!-- Analyses -->
            <div class="analyse">

            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->

            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">

                <?php
                echo "<h2>Loan Applications</h2>";
                include 'loan_application_table.php';
                echo "<h2>Health Insurance Applications</h2>";
                include 'health_insurance_application_table.php';
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
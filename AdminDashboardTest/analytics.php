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
    <!-- Montserrat Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/cards.css">
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
            <h1>Analytics</h1>
            <!-- Analyses -->

            <div class="analyse">
                <?php
                include 'chart/overall_card.php';
                ?>
            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="analyse">
            </div>
            <br><br><br><br>
            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->


            <div class="container" style="margin-top:50">

                <div class="chart">
                    <canvas id="barchart" width="300px" height="300px"></canvas>
                </div>

                <div class="chart">
                    <canvas id="doughnut" width="300px" height="300px"></canvas>

                </div>

            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>


            <?php
            include 'chart/chart.php';
            ?>
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
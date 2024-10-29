<?php

require 'check_user.php';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
            <h1>Income Statement</h1>
            <!-- Analyses -->
            <div class="analyse">

            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="new-users">


            </div>
            <!-- End of New Users Section -->
            <div class="recent-orders">
                <div class="date-range-form"
                    style="margin: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background-color: #f9f9f9;">
                    <h2 style="text-align: center; margin-bottom: 20px;">Select Date Range</h2>
                    <form method="GET" action="">
                        <label for="start_date" style="margin-right: 10px;">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" required style="margin-right: 20px;">
                        <label for="end_date" style="margin-right: 10px;">End Date:</label>
                        <input type="date" id="end_date" name="end_date" required style="margin-right: 20px;">
                        <button type="submit" style="padding: 5px 15px;">Filter</button>
                    </form>
                </div>

                <!-- Ledger Table -->
                <?php

                if ($role === 'Admin' || 'Finance Officer'):

                    include 'balance_sheet_assets.php';
                    echo "<br>";
                    include 'balance_sheet_liabilities.php';
                    echo "<br>";
                    include 'balance_sheet_equity.php';
                    echo "<br>";


                endif;
                ?>





                <script>
                    function toggleCollapse(id) {
                        var content = document.getElementById(id);
                        var rows = document.getElementsByClassName(id);
                        if (content.style.display === "none") {
                            content.style.display = "block";
                            for (var i = 0; i < rows.length; i++) {
                                rows[i].style.display = "table-row";
                            }
                        } else {
                            content.style.display = "none";
                            for (var i = 0; i < rows.length; i++) {
                                rows[i].style.display = "none";
                            }
                        }
                    }
                </script>
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
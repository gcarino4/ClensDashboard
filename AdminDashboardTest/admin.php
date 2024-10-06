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




    </div>



</body>

</html>
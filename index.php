<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/jquery-3.6.4.min.js"></script>
    <title>Login</title>
</head>

<body class="bg-primary">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="charts/chart1.js"></script>
    <script src="charts/chart2.js"></script>
    <div class="container mt-5 py-5">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-5 mb-2">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        CoLens System
                    </div>

                    <div class="card-body">
                        <div id="alert"></div>
                        <form id="login">
                            <div class="form-group">
                                <input class="form-control" type="text" name="username" id="username"
                                    placeholder="Enter Username or Email">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="password" name="password" id="password"
                                    placeholder="Enter Password">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success btn-block" type="submit">Login</button>
                                <a class="btn btn-success btn-block" href="register.html">Register</a>
                                <!-- Forgot Password Link -->
                                <a class="btn btn-link btn-block" href="forgot_password.php">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                $('#login').submit(function (e) {
                    e.preventDefault();

                    var username = $('#username').val();
                    var password = $('#password').val();

                    if (username.length == 0 || password.length == 0) {
                        $('#alert').html("<div class='alert alert-danger'>Username and Password are required!</div>");
                        $('#alert').fadeIn(500); // Show alert
                        setTimeout(function () {
                            $('#alert').fadeOut(1000); // Hide alert after 1 second
                        }, 3000); // Display alert for 3 seconds
                    } else {
                        $.ajax({
                            url: 'login.php',
                            type: 'POST',
                            data: $(this).serialize(),
                            success: function (response) {
                                // Check if the response is one of the admin roles
                                if (response === "Admin" || response === "Admin Officer" || response === "Finance Officer") {
                                    location.replace("AdminDashboardTest/index.php");
                                } else if (response === "Member") {
                                    location.replace("AdminDashboardTest/index_member.php");
                                } else {
                                    $('#alert').html("<div class='alert alert-danger'>" + response + "</div>");
                                    $('#alert').fadeIn(500); // Show alert
                                    setTimeout(function () {
                                        $('#alert').fadeOut(1000); // Hide alert after 1 second
                                    }, 3000); // Display alert for 3 seconds
                                }
                            },
                            error: function () {
                                $('#alert').html("<div class='alert alert-danger'>An error occurred. Please try again later.</div>");
                                $('#alert').fadeIn(500); // Show alert
                                setTimeout(function () {
                                    $('#alert').fadeOut(1000); // Hide alert after 1 second
                                }, 3000); // Display alert for 3 seconds
                            }
                        });

                    }
                });
            });
        </script>

        <style>
            body {
                background-image: url("colen.jpg");
                background-size: cover;
                /* Cover the entire background */
                background-repeat: no-repeat;
                background-position: center center;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                flex-direction: column;
            }

            #image-container {
                position: absolute;
                top: 14%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            #royeca-image {
                width: 195px;
                /* Adjust width as needed */
                height: auto;
                /* Maintain aspect ratio */
            }

            .card {
                background-color: rgba(255, 255, 255, 0.5);
                /* Background with transparency */
                backdrop-filter: blur(3px);
                /* Blur effect */
            }

            /* Media Query for smaller screens */
            @media only screen and (max-width: 600px) {
                #royeca-image {
                    width: 120px;
                    /* Adjust width for smaller screens */
                }
            }
        </style>

        <script src="js/index.js"></script>
</body>

</html>
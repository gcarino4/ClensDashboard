<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/jquery-3.6.4.min.js"></script>
    <title></title>
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
                            </div>
                            <div class="form-group">
                                <style>
                                    #forgotPassword {
                                        float: right;
                                        color: black;
                                        font-weight: bold;
                                    }
                                </style>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog"
            aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="forgotPasswordForm">
                            <div class="form-group">
                                <label for="forgotUsername">Enter Your Username:</label><br>
                                <input class="form-control" type="text" name="forgotUsername" id="forgotUsername"
                                    placeholder="Enter Username">
                            </div>
                            <div id="newPasswordInput" style="display: none;">
                                <div class="form-group">
                                    <label for="newPassword">Enter New Password:</label>
                                    <input class="form-control" type="password" name="newPassword" id="newPassword"
                                        placeholder="Enter New Password">
                                </div>
                            </div>
                            <div id="forgotPasswordError" class="text-danger" style="display: none;">Username not found.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="savePassBtn">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Show forgot password modal
                document.getElementById("forgotPassword").addEventListener("click", function () {
                    $('#forgotPasswordModal').modal('show');
                    // Reset the form
                    document.getElementById("forgotPasswordForm").reset();
                    // Hide newPasswordInput and forgotPasswordError initially
                    document.getElementById("newPasswordInput").style.display = "none";
                    document.getElementById("forgotPasswordError").style.display = "none";
                    // Hide savePassBtn initially
                    document.getElementById("savePassBtn").style.display = "none";
                });

                // Check username function
                function checkUsername() {
                    var username = document.getElementById("forgotUsername").value.trim();

                    // Make AJAX request to check if username exists
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "check_username.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            if (xhr.responseText == "exists") {
                                // Username exists
                                document.getElementById("newPasswordInput").style.display = "block";
                                document.getElementById("forgotPasswordError").style.display = "none";
                                // Show savePassBtn
                                document.getElementById("savePassBtn").style.display = "block";
                            } else {
                                // Username does not exist
                                document.getElementById("newPasswordInput").style.display = "none";
                                document.getElementById("forgotPasswordError").style.display = "block";
                                // Hide savePassBtn
                                document.getElementById("savePassBtn").style.display = "none";
                            }
                        }
                    };
                    xhr.send("username=" + username);
                }

                // Event listener for the username input field
                document.getElementById("forgotUsername").addEventListener("input", function () {
                    // When there's an input, directly check the username
                    checkUsername();
                });

                // Save changes button click
                document.getElementById("savePassBtn").addEventListener("click", function () {
                    var username = document.getElementById("forgotUsername").value.trim();
                    var newPassword = document.getElementById("newPassword").value;

                    // Make AJAX request to update password
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "update_password.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            alert(xhr.responseText); // Show success or error message
                            $('#forgotPasswordModal').modal('hide'); // Close modal
                        }
                    };
                    xhr.send("username=" + username + "&newPassword=" + newPassword);
                });

                // Trigger savePassBtn click when Enter key is pressed in the new password input field
                document.getElementById("newPassword").addEventListener("keypress", function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        document.getElementById("savePassBtn").click();
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
        <script src="js/index.js"></script>
</body>

</html>
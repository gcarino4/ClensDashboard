<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to the external CSS file -->

</head>

<style>
    /* General styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7fc;
        color: #333;
    }

    .container {
        max-width: 500px;
        margin: 0 auto;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        font-size: 1.5rem;
        padding: 15px;
        text-transform: uppercase;
    }

    .card-body {
        padding: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
        margin-top: 5px;
    }

    button {
        padding: 12px;
        font-size: 1.1rem;
        width: 100%;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }

    button.btn-primary {
        background-color: #007bff;
        color: white;
    }

    button.btn-primary:hover {
        background-color: #0056b3;
    }

    .alert {
        margin-top: 10px;
        font-size: 1rem;
    }

    /* For smaller screens */
    @media (max-width: 600px) {
        .container {
            padding: 10px;
        }

        .card {
            margin-top: 10px;
        }
    }
</style>

<body class="bg-light">
    <div class="container">
        <div class="card mt-5">
            <div class="card-header bg-primary text-white text-center">
                Forgot Your Password?
            </div>
            <div class="card-body">
                <form action="forgot_password.php" method="POST" id="forgotPasswordForm">
                    <div class="form-group">
                        <label for="email">Enter your registered email address:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                </form>
                <div id="alert"></div>
                <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#forgotPasswordForm').submit(function (e) {
                e.preventDefault();
                var email = $('#email').val();

                if (email.length == 0) {
                    $('#alert').html("<div class='alert alert-danger'>Email is required!</div>");
                    $('#alert').fadeIn(500);
                    setTimeout(function () {
                        $('#alert').fadeOut(1000);
                    }, 3000);
                } else {
                    $.ajax({
                        url: 'forgot_password_action.php',
                        type: 'POST',
                        data: { email: email },
                        success: function (response) {
                            $('#alert').html("<div class='alert alert-success'>" + response + "</div>");
                            $('#alert').fadeIn(500);
                            setTimeout(function () {
                                $('#alert').fadeOut(1000);
                            }, 3000);
                        },
                        error: function () {
                            $('#alert').html("<div class='alert alert-danger'>An error occurred. Please try again later.</div>");
                            $('#alert').fadeIn(500);
                            setTimeout(function () {
                                $('#alert').fadeOut(1000);
                            }, 3000);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
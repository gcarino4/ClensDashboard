<?php
require 'check_user.php';
require 'connection.php';

// Fetch user data from the members table
$member_id = $_SESSION['member_id']; // Assuming member_id is stored in session after login

$sql = "SELECT * FROM members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user not found, redirect to an error page or handle appropriately
if (!$user) {
    echo "User not found!";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Profile Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .main {
            flex: 1;
            padding: 20px;
        }

        .profile-info {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .profile-info h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .profile-info .card {
            margin-top: 20px;
        }

        .profile-info img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        .profile-info form {
            margin-top: 10px;
        }

        .profile-info table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .profile-info table th,
        .profile-info table td {
            padding: 10px;
            text-align: left;
        }

        .profile-info table th {
            background: #f4f4f4;
        }

        .profile-info table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .profile-info table td {
            border-bottom: 1px solid #ddd;
        }

        .upload-btn {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .upload-btn:hover {
            background: #0056b3;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal h2 {
            text-align: center;
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
        }

        .closeCP {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
        }

        .closeCP:hover,
        .closeCP:focus {
            color: #000;
            text-decoration: none;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 8px;
        }

        .modal-input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .modal-input:focus {
            border-color: #007bff;
            outline: none;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include "sidebar.php"; ?>

        <!-- Main Content -->
        <main class="main">
            <div class="profile-info card p-4">
                <h1>Your Profile</h1>

                <!-- Profile Picture Upload Section -->
                <div class="text-center mb-4">
                    <img src="<?php echo !empty($user['profile_pic']) ? 'uploads/' . htmlspecialchars($user['profile_pic']) : 'images/default-profile.png'; ?>"
                        alt="Profile Picture">
                    <form action="upload_profile_pic.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_pic" accept="image/*" required>
                        <button type="submit" class="upload-btn">Upload New Picture</button>
                    </form>


                </div>

                <!-- Change Password Button -->
                <button id="change-password-btn" class="upload-btn">Change Password</button>

                <!-- Change Password Modal -->
                <div id="change-password-modal" class="modal">
                    <div class="modal-content">
                        <span class="closeCP">&times;</span>
                        <h2>Change Your Password</h2>

                        <form action="change_password.php" method="POST">
                            <div class="form-group">
                                <label for="current-password">Current Password</label>
                                <input type="password" name="current_password" id="current-password" required
                                    class="modal-input">
                            </div>

                            <div class="form-group">
                                <label for="new-password">New Password</label>
                                <input type="password" name="new_password" id="new-password" required
                                    class="modal-input">
                            </div>

                            <div class="form-group">
                                <label for="confirm-password">Confirm New Password</label>
                                <input type="password" name="confirm_password" id="confirm-password" required
                                    class="modal-input">
                            </div>

                            <button type="submit" class="submit-btn">Change Password</button>
                        </form>
                    </div>
                </div>



                <!-- Personal Information Table -->
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <th class="text-end">Member ID:</th>
                            <td><?php echo htmlspecialchars($user['member_id']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Name:</th>
                            <td><?php echo htmlspecialchars($user['name']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Age:</th>
                            <td><?php echo htmlspecialchars($user['age']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Birthday:</th>
                            <td><?php echo htmlspecialchars($user['birthday']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Birth Place:</th>
                            <td><?php echo htmlspecialchars($user['place_of_birth']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Gender:</th>
                            <td><?php echo htmlspecialchars($user['sex']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Civil Status:</th>
                            <td><?php echo htmlspecialchars($user['civil_status']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Spouse Name:</th>
                            <td><?php echo htmlspecialchars($user['spouse_name']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Children:</th>
                            <td><?php echo nl2br(htmlspecialchars($user['children']) ?: 'Not available'); ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Education:</th>
                            <td>
                                <?php
                                echo "Date: " . nl2br(htmlspecialchars($user['education_date']) ?: 'Not available') . "<br>";
                                echo "School: " . nl2br(htmlspecialchars($user['education_school']) ?: 'Not available') . "<br>";
                                echo "Course: " . nl2br(htmlspecialchars($user['education_course']) ?: 'Not available') . "<br>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-end">Employment:</th>
                            <td>
                                <?php
                                echo "Date: " . nl2br(htmlspecialchars($user['employment_date']) ?: 'Not available') . "<br>";
                                echo "Position: " . nl2br(htmlspecialchars($user['employment_position']) ?: 'Not available') . "<br>";
                                echo "School: " . nl2br(htmlspecialchars($user['employment_school']) ?: 'Not available') . "<br>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-end">Cooperative Experience:</th>
                            <td>
                                <?php
                                echo "Date: " . nl2br(htmlspecialchars($user['cooperative_experience_date']) ?: 'Not available') . "<br>";
                                echo "Position: " . nl2br(htmlspecialchars($user['cooperative_experience_position']) ?: 'Not available') . "<br>";
                                echo "Name: " . nl2br(htmlspecialchars($user['cooperative_experience_name']) ?: 'Not available') . "<br>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-end">Training:</th>
                            <td>
                                <?php
                                echo "Date: " . nl2br(htmlspecialchars($user['training_date']) ?: 'Not available') . "<br>";
                                echo "Course: " . nl2br(htmlspecialchars($user['training_course']) ?: 'Not available') . "<br>";
                                echo "Hours: " . nl2br(htmlspecialchars($user['training_hours']) ?: 'Not available') . "<br>";
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-end">Business Present:</th>
                            <td><?php echo nl2br(htmlspecialchars($user['business_present']) ?: 'Not available'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-end">Business Previous:</th>
                            <td><?php echo nl2br(htmlspecialchars($user['business_previous']) ?: 'Not available'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-end">Affiliation:</th>
                            <td><?php echo nl2br(htmlspecialchars($user['affiliation']) ?: 'Not available'); ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Member Salary:</th>
                            <td><?php echo htmlspecialchars($user['member_salary']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Spouse Income:</th>
                            <td><?php echo htmlspecialchars($user['spouse_income']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Other Income:</th>
                            <td><?php echo htmlspecialchars($user['other_income']) ?: 'Not available'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Crime:</th>
                            <td><?php echo nl2br(htmlspecialchars($user['crime']) ?: 'Not available'); ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Address:</th>
                            <td><?php echo nl2br(htmlspecialchars($user['address']) ?: 'Not available'); ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Email:</th>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Role:</th>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Verified:</th>
                            <td><?php echo htmlspecialchars($user['verified']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <script>
            // Get modal and button elements
            const modal = document.getElementById("change-password-modal");
            const btn = document.getElementById("change-password-btn");
            const closeBtn = document.getElementsByClassName("closeCP")[0];

            // When the user clicks the button, open the modal
            btn.onclick = function () {
                modal.style.display = "block";
            }

            // When the user clicks the close button, close the modal
            closeBtn.onclick = function () {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside the modal, close it
            window.onclick = function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            }
        </script>

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>

                <!-- Profile Section -->
                <?php include 'profile.php'; ?>
            </div>
        </div>
    </div>
</body>

</html>
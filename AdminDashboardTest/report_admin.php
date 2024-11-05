<?php
ob_start();
include 'connection.php'; // Assuming this file contains your database connection details
include 'check_user.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/cards.css">
    <title>CoLens Dashboard Design</title>

    <style>
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-bar input[type="text"] {
            width: 80%;
            padding: 10px;
            font-size: 16px;
        }

        .search-bar button {
            width: 30%;
            padding: 10px;
            background-color: green;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-left: 40px;
        }

        .report-output {
            margin-top: 20px;
            margin-left: 200px;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f4f4f4;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            align-items: center;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-header h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .report-details {
            margin-bottom: 20px;
        }

        .report-section {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .report-section:last-child {
            border-bottom: none;
        }

        .report-section label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        .report-section p {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .no-results {
            color: red;
            text-align: center;
            font-size: 1.2em;
            margin-top: 20px;
        }
    </style>

</head>

<body>

    <div class="container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main>
            <h1>Report</h1>

            <div class="recent-orders">
                <form id="searchForm" method="POST" action="">
                    <div class="search-bar">
                        <input type="text" name="application_id"
                            placeholder="Enter Application ID (e.g., loan1234 or health1234)" required>
                        <button type="submit" name="search" class="btn">Generate Report</button>
                    </div>
                </form>

                <div class="report-output">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search']) && !empty($_POST['application_id'])) {
                        include 'connection.php'; // Database connection
                    
                        $application_id = $_POST['application_id'];

                        // Determine if the application ID is for a loan or health application
                        if (strpos($application_id, 'loan') === 0) {
                            $sql = "SELECT la.*, al.next_payment_due_date, al.loan_end_date, al.minimum_payment 
                                    FROM loan_applications la 
                                    JOIN approved_loans al ON la.application_id = al.application_id 
                                    WHERE la.application_id = ?";
                            $comaker_sql = "SELECT comaker_name FROM comakers WHERE application_id = ?";

                        } elseif (strpos($application_id, 'hlt') === 0) {
                            // Health insurance application query
                            $sql = "SELECT * FROM health_insurance_applications WHERE application_id = ?";

                        } else {
                            echo "<p class='no-results'>Invalid Application ID format.</p>";
                            exit;
                        }

                        // Prepare and execute the query
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $application_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Check if any records were found
                        if ($result->num_rows > 0) {
                            if (strpos($application_id, 'loan') === 0) {
                                echo "<div class='report-header'><h2>Loan Application Report</h2></div>";
                            } else {
                                echo "<div class='report-header'><h2>Health Insurance Application Report</h2></div>";
                            }

                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='report-details'>";
                                echo "<div class='report-section'>
                                        <label>Application ID:</label>
                                        <p>{$row['application_id']}</p>
                                      </div>";
                                echo "<div class='report-section'>
                                        <label>Name:</label>
                                        <p>{$row['name']}</p>
                                      </div>";

                                if (strpos($application_id, 'loan') === 0) {
                                    echo "<div class='report-section'>
                                                <label>Member ID:</label>
                                                <p>{$row['member_id']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Email:</label>
                                                <p>{$row['email']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Phone Number:</label>
                                                <p>{$row['phone_number']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Address:</label>
                                                <p>{$row['address']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Annual Income:</label>
                                                <p>{$row['annual_income']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Bank Info:</label>
                                                <p>{$row['bank_name']} - {$row['bank_id']} - {$row['branch']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Loan Amount + Interest:</label>
                                                <p>{$row['loan_amount']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Principal Amount:</label>
                                                <p>{$row['principal_amount']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Loan Term:</label>
                                                <p>{$row['loan_term']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Loan Type:</label>
                                                <p>{$row['loan_purpose']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Collateral:</label>
                                                <p>{$row['collateral']}</p>
                                              </div>";

                                    // Display co-maker names
                                    $comaker_stmt = $conn->prepare($comaker_sql);
                                    $comaker_stmt->bind_param("s", $application_id);
                                    $comaker_stmt->execute();
                                    $comaker_result = $comaker_stmt->get_result();

                                    if ($comaker_result->num_rows > 0) {
                                        echo "<div class='report-section'>
                                                          <label>Co-maker Names:</label>";
                                        echo "<ul>";
                                        while ($comaker_row = $comaker_result->fetch_assoc()) {
                                            echo "<li>{$comaker_row['comaker_name']}</li>";
                                        }
                                        echo "</ul>";
                                        echo "</div>";
                                    } else {
                                        echo "<div class='report-section'>
                                                          <label>Co-maker Name:</label>
                                                          <p>No Co-maker</p>
                                                        </div>";
                                    }

                                    $comaker_stmt->close(); // Close the statement
                    
                                    echo "<div class='report-section'>
                                                <label>Next Payment Due Date:</label>
                                                <p>{$row['next_payment_due_date']}</p>
                                            </div>";
                                    echo "<div class='report-section'>
                                                <label>Loan End Date:</label>
                                                <p>{$row['loan_end_date']}</p>
                                            </div>";
                                    echo "<div class='report-section'>
                                                <label>Minimum Payment:</label>
                                                <p>{$row['minimum_payment']}</p>
                                            </div>";
                                    echo "<div class='report-section'>
                                                <label>Collateral Image:</label>
                                                <img src='data:image/jpeg;base64," . htmlspecialchars($row["collateral_image"]) . "' class='img-preview' alt='No Collateral Image' onclick='openModal(this.src)'/>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Application Date:</label>
                                                <p>{$row['application_date']}</p>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Supporting Document 1:</label>
                                                <img src='data:image/jpeg;base64," . htmlspecialchars($row["supporting_document_1"]) . "' class='img-preview' alt='No Document' onclick='openModal(this.src)'/>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Supporting Document 2:</label>
                                                <img src='data:image/jpeg;base64," . htmlspecialchars($row["supporting_document_2"]) . "' class='img-preview' alt='No Document' onclick='openModal(this.src)'/>
                                              </div>";
                                    echo "<div class='report-section'>
                                                <label>Application Status:</label>
                                                <p>{$row['status']}</p>
                                              </div>";
                                    echo "</div>"; // Close report-details div
                                } else {
                                    // Health insurance application details
                                    echo "<div class='report-section'>
                                            <label>Member ID:</label>
                                            <p>{$row['member_id']}</p>
                                          </div>";
                                    echo "<div class='report-section'>
                                            <label>Email:</label>
                                            <p>{$row['email']}</p>
                                          </div>";
                                    echo "<div class='report-section'>
                                            <label>Phone Number:</label>
                                            <p>{$row['phone_number']}</p>
                                          </div>";
                                    echo "<div class='report-section'>
                                            <label>Policy Type:</label>
                                            <p>{$row['policy_type']}</p>
                                          </div>";
                                    echo "<div class='report-section'>
                                            <label>Coverage Amount:</label>
                                            <p>{$row['coverage_amount']}</p>
                                          </div>";
                                    echo "<div class='report-section'>
                                            <label>Application Date:</label>
                                            <p>{$row['application_date']}</p>
                                          </div>";
                                    echo "<div class='report-section'>
                                            <label>Application Status:</label>
                                            <p>{$row['status']}</p>
                                          </div>";
                                    echo "</div>"; // Close report-details div
                                }
                            }
                        } else {
                            echo "<p class='no-results'>No records found for this Application ID.</p>";
                        }

                        $stmt->close(); // Close the statement
                        $conn->close(); // Close the database connection
                    }
                    ?>
                </div>
            </div>
            <script>
                function openModal(imageSrc) {
                    // Code to open modal and display image
                    const modal = document.createElement('div');
                    modal.style.position = 'fixed';
                    modal.style.top = '0';
                    modal.style.left = '0';
                    modal.style.width = '100%';
                    modal.style.height = '100%';
                    modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
                    modal.style.display = 'flex';
                    modal.style.alignItems = 'center';
                    modal.style.justifyContent = 'center';

                    const img = document.createElement('img');
                    img.src = imageSrc;
                    img.style.maxWidth = '90%';
                    img.style.maxHeight = '90%';

                    modal.appendChild(img);
                    document.body.appendChild(modal);

                    modal.addEventListener('click', function () {
                        modal.remove();
                    });
                }
            </script>
            <script>
                // Prevent resubmission on page reload by clearing the form after submission
                document.getElementById('searchForm').onsubmit = function () {
                    setTimeout(function () {
                        document.getElementById('searchForm').reset();
                    }, 10);
                };
            </script>

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>

                <?php include 'profile.php'; ?>
            </div>
            <div class="user-profile">
                <div class="logo">
                    <img src="images/CoLens.png">
                    <h2>Accounting Management System</h2>
                    <p>GSCTEMPCO</p>
                </div>
            </div>

            <?php include 'verified_notification.php'; ?>
        </div>

    </div>

</body>

</html>

<?php ob_end_flush(); // Flush the output buffer and send headers ?>
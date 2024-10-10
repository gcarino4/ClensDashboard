<?php
require 'check_user.php';
include 'connection.php';

// Initialize eligibility variables
$canApply = false;
$isContributionEligible = false; // New variable for contribution eligibility

// Check if the user is logged in and has a member ID
if (isset($_SESSION['member_id'])) {
    $member_id = $_SESSION['member_id'];

    // Query to get the date_of_creation based on member_id
    $sql = "SELECT date_of_creation FROM members WHERE member_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $date_of_creation = new DateTime($row['date_of_creation']);
            $current_date = new DateTime();
            $interval = $current_date->diff($date_of_creation);

            // Check if the interval is greater than or equal to 6 months
            if ($interval->m >= 6 || $interval->y > 0) {
                $canApply = true; // Eligible to apply
            } else {
                $canApply = false; // Not eligible to apply
            }
        } else {
            echo "No member found!";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }

    // Query to get total contribution amount
    $sql = "SELECT SUM(contribution_amount) AS total_contribution FROM contributions WHERE member_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalContribution = $row['total_contribution'];

            // Check if total contribution is over 10,000
            if ($totalContribution >= 10000) {
                $isContributionEligible = true; // Eligible based on contribution
            } else {
                $isContributionEligible = false; // Not eligible based on contribution
            }
        } else {
            echo "No contributions found!";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/application.css">

    <script src="index.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>CoLens Dashboard</title>
    <script>

        const canApply = <?php echo json_encode($canApply); ?>; // Pass eligibility status to JS
    </script>
</head>

<body>

    <div class="container">
        <?php include "sidebar.php"; ?>

        <!-- Main Content -->
        <main>
            <h1>Welcome, <?php echo htmlspecialchars($displayName); ?></h1>

            <!-- Analyses -->
            <div class="analyse">
                <!-- Your analyses content -->
            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="new-users">

                <!-- Your new users content -->
            </div>
            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">


                <h2>Application</h2>
                <!-- Buttons to open modals -->
                <button id="openLoanApplicationModalBtn" class="btn-large centered" data-action="loan-application">
                    <i class="fas fa-money-check-alt"></i> Apply for Loan
                </button>
                <button id="openHealthInsuranceModalBtn" class="btn-large centered" data-action="health-insurance">
                    <i class="fas fa-file-medical"></i> Apply for Insurance
                </button>


                <div id="loanApplicationModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeLoanApplicationModal">&times;</span>

                        <h2>Loan Application</h2>
                        <form id="loanApplicationForm" method="post" action="submit_loan_application.php"
                            enctype="multipart/form-data">
                            <!-- Member ID Field (Visible) -->
                            <label for="memberId">Member ID:</label>
                            <input type="text" id="memberId" name="member_id"
                                value="<?php echo isset($_SESSION['member_id']) ? htmlspecialchars($_SESSION['member_id']) : ''; ?>"
                                readonly><br>

                            <label for="applicantName">Name:</label>
                            <input type="text" id="applicantName" name="name"
                                value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>"
                                readonly required><br>

                            <label for="applicantEmail">Email:</label>
                            <input type="email" id="applicantEmail" name="email"
                                value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"
                                readonly required><br>

                            <label for="applicantPhone">Phone Number:</label>
                            <input type="text" id="applicantPhone" name="phone_number"
                                value="<?php echo isset($_SESSION['contact_no']) ? htmlspecialchars($_SESSION['contact_no']) : ''; ?>"
                                readonly required><br>

                            <label for="applicantAddress">Address:</label>
                            <input type="text" id="applicantAddress" name="address"
                                value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>"
                                readonly required><br>

                            <label for="bank_info">Bank Detail:</label>
                            <input type="text" id="bank_info" name="bank_info" required><br>


                            <label for="applicantIncome">Annual Income:</label>
                            <input type="number" id="applicantIncome" name="annual_income" step="0.01" required><br>

                            <label for="loanAmount">Loan Amount Requested:</label>
                            <input type="number" id="loanAmount" name="loan_amount" step="0.01" required><br>

                            <label for="loanTerm">Loan Term:</label>
                            <select id="loanTerm" name="loan_term" required>
                                <option value="1">6 Months - 1 Year</option>
                                <option value="3">1 Year - 3 Years</option>
                                <option value="5">3 Years - 5 Years</option>
                            </select><br>

                            <label for="loanPurpose">Purpose of Loan:</label>
                            <select id="loanPurpose" name="loan_purpose" required>
                                <option value="Home">Home</option>
                                <option value="Car">Car</option>
                                <option value="Education">Education</option>
                                <option value="Personal">Personal</option>
                                <option value="Other">Other</option>
                            </select><br>

                            <label for="employmentStatus">Employment Status:</label>
                            <select id="employmentStatus" name="employment_status" required>
                                <option value="Employed">Employed</option>
                                <option value="Self-Employed">Self-Employed</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Retired">Retired</option>
                            </select><br>

                            <label for="collateral">Collateral:</label>
                            <input type="text" id="collateral" name="collateral"><br>

                            <!-- Add File Uploads for Supporting Documents -->
                            <label for="collateral_image">Collatteral:</label>
                            <input type="file" id="collateral_image" name="collateral_image" accept="image/*">
                            <br>

                            <!-- Payment Plan Dropdown -->
                            <label for="paymentPlan">Payment Plan:</label>
                            <select id="paymentPlan" name="payment_plan" required>
                                <option value="">Select Payment Plan</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annually">Annually</option>
                            </select><br>

                            <!-- Add File Uploads for Supporting Documents -->
                            <label for="supportingDocument1">Co- Makers Statement document (Image):</label>
                            <input type="file" id="supportingDocument1" name="supporting_document_1" accept="image/*"
                                required><br>

                            <label for="supportingDocument2">Deed of Assignment document (Image):</label>
                            <input type="file" id="supportingDocument2" name="supporting_document_2"
                                accept="image/*"><br>

                            <button type="submit" id="loanSubmitBtn">Submit Application</button>
                        </form>
                    </div>
                </div>


                <div id="healthInsuranceModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeHealthInsuranceModal">&times;</span>

                        <h2>Health Insurance Application</h2>
                        <form id="healthInsuranceForm" method="post" action="submit_health_insurance.php">
                            <!-- Existing Fields -->
                            <label for="memberId">Member ID:</label>
                            <input type="text" id="memberId" name="member_id"
                                value="<?php echo isset($_SESSION['member_id']) ? htmlspecialchars($_SESSION['member_id']) : ''; ?>"
                                readonly><br>

                            <label for="applicantName">Name:</label>
                            <input type="text" id="applicantName" name="name"
                                value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>"
                                readonly required><br>

                            <label for="applicantEmail">Email:</label>
                            <input type="email" id="applicantEmail" name="email"
                                value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"
                                readonly required><br>

                            <label for="applicantPhone">Phone Number:</label>
                            <input type="text" id="applicantPhone" name="phone_number"
                                value="<?php echo isset($_SESSION['contact_no']) ? htmlspecialchars($_SESSION['contact_no']) : ''; ?>"
                                readonly required><br>

                            <label for="applicantAddress">Address:</label>
                            <input type="text" id="applicantAddress" name="address"
                                value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>"
                                readonly required><br>

                            <label for="applicantbirthday">Date of Birth:</label>
                            <input type="date" id="applicantbirthday" name="birthday"
                                value="<?php echo isset($_SESSION['birthday']) ? htmlspecialchars($_SESSION['birthday']) : ''; ?>"
                                readonly required><br>

                            <label for="insuranceType">Insurance Type:</label>
                            <select id="insuranceType" name="insurance_type" required>
                                <option value="Individual">Individual</option>
                                <option value="Family">Family</option>
                                <option value="Senior Citizen">Senior Citizen</option>
                            </select><br>

                            <label for="coverageAmount">Coverage Amount:</label>
                            <input type="number" id="coverageAmount" name="coverage_amount" step="0.01" required><br>

                            <!-- Beneficiary Section -->
                            <h3>Beneficiaries</h3>
                            <div id="beneficiaries">
                                <div class="beneficiary">
                                    <label for="beneficiaryName">Beneficiary Name:</label>
                                    <input type="text" name="beneficiary_name[]"><br>

                                    <label for="beneficiaryRelationship">Relationship:</label>
                                    <input type="text" name="beneficiary_relationship[]"><br>

                                    <label for="beneficiaryDOB">Date of Birth:</label>
                                    <input type="date" name="beneficiary_dob[]"><br>
                                </div>
                            </div>
                            <button type="button" id="addBeneficiaryBtn">Add Another Beneficiary</button><br>

                            <!-- Payment Plan and Coverage Term Fields -->
                            <label for="paymentPlan">Payment Plan:</label>
                            <select id="paymentPlan" name="payment_plan" required>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Annually">Annually</option>
                            </select><br>

                            <label for="coverageTerm">Coverage Term:</label>
                            <select id="coverageTerm" name="coverage_term" required>
                                <option value="1">1 Year</option>
                                <option value="2">2 Years</option>
                                <option value="3">3 Years</option>
                                <option value="5">5 Years</option>
                            </select><br>

                            <button type="submit" id="healthInsuranceSubmitBtn">Submit Application</button>
                        </form>
                    </div>
                </div>

                <?php
                include 'pending_applications.php';
                ?>

                <!-- JavaScript to add more beneficiaries -->
                <script src="add_beneficiary.js"></script>

                <!-- Style for the Modal (optional) -->
                <style>
                    .modal {
                        display: none;
                        /* Hidden by default */
                        position: fixed;
                        /* Stay in place */
                        z-index: 1;
                        /* Sit on top */
                        left: 0;
                        top: 0;
                        width: 100%;
                        /* Full width */
                        height: 100%;
                        /* Full height */
                        overflow: auto;
                        /* Enable scroll if needed */
                        background-color: rgb(0, 0, 0);
                        /* Fallback color */
                        background-color: rgba(0, 0, 0, 0.4);
                        /* Black w/ opacity */
                    }

                    .modal-content {
                        background-color: #fefefe;
                        margin: 15% auto;
                        /* 15% from the top and centered */
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                        /* Could be more or less, depending on screen size */
                    }
                </style>

                <!-- Modal for eligibility information -->
                <div id="eligibilityModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" id="closeEligibilityModal">&times;</span>
                        <!-- Ensure this matches your JavaScript -->
                        <h2>Eligibility Information</h2>
                        <p>You need to be at least 6 months member first.</p>
                    </div>
                </div>

                <!-- Modal for verification notice -->
                <div id="verificationModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" id="closeVerificationModal">&times;</span>
                        <!-- Ensure this has a unique ID -->
                        <p>Please get verified first to apply.</p>
                    </div>
                </div>

                <!-- Loan Application Modal -->
                <div id="loanApplicationModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeLoanApplicationModal">&times;</span>
                        <h2>Loan Application</h2>
                        <form id="loanApplicationForm" method="post" action="submit_loan_application.php">
                            <!-- Your form fields -->
                        </form>
                    </div>
                </div>

                <!-- Health Insurance Modal -->
                <div id="healthInsuranceModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeHealthInsuranceModal">&times;</span>
                        <h2>Health Insurance Application</h2>
                        <form id="healthInsuranceForm" method="post" action="submit_health_insurance.php">
                            <!-- Your form fields -->
                        </form>
                    </div>
                </div>

                <div id="contributionModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" id="closeContributionModal">&times;</span>
                        <h2>Eligibility Information</h2>
                        <p>Your total contributions must exceed 10,000 to apply.</p>
                    </div>
                </div>


                <script>

                    document.addEventListener('DOMContentLoaded', function () {
                        // Modal elements
                        const loanApplicationModal = document.getElementById('loanApplicationModal');
                        const healthInsuranceModal = document.getElementById('healthInsuranceModal');
                        const savingsApplicationModal = document.getElementById('savingsApplicationModal');
                        const verificationModal = document.getElementById('verificationModal');
                        const eligibilityModal = document.getElementById('eligibilityModal');
                        const contributionModal = document.getElementById('contributionModal');
                        // Open buttons
                        const openLoanApplicationModalBtn = document.getElementById('openLoanApplicationModalBtn');
                        const openHealthInsuranceModalBtn = document.getElementById('openHealthInsuranceModalBtn');

                        // Close buttons
                        const closeLoanApplicationModal = document.getElementById('closeLoanApplicationModal');
                        const closeHealthInsuranceModal = document.getElementById('closeHealthInsuranceModal');
                        const closeSavingsApplicationModal = document.getElementById('closeSavingsApplicationModal');
                        const closeVerificationModal = document.getElementById('closeVerificationModal');
                        const closeEligibilityModal = document.getElementById('closeEligibilityModal');
                        const closeContributionModal = document.getElementById('closeContributionModal');

                        // Assuming isVerified, canApply, and isContributionEligible variables are set from the server-side PHP code
                        const isVerified = <?php echo json_encode($_SESSION['verified'] === 'True'); ?>; // Pass verified status to JS
                        const canApply = <?php echo json_encode($canApply); ?>; // Pass eligibility to JS
                        const isContributionEligible = <?php echo json_encode($isContributionEligible); ?>; // Pass contribution eligibility to JS

                        // Function to open the modal based on verification, eligibility, and contribution status
                        function openModal(modal) {
                            if (!isVerified) {
                                verificationModal.style.display = 'block'; // Show verification modal if not verified
                            }

                            else if (!canApply) {
                                eligibilityModal.style.display = 'block'; // Show eligibility modal if not eligible
                            }

                            else if (!isContributionEligible) {
                                contributionModal.style.display = 'block'; // Show eligibility modal if contribution is not eligible
                            }
                            else {
                                modal.style.display = 'block'; // Show the intended modal if both verified and eligible
                            }


                        }

                        // Open modals based on verification and eligibility
                        openLoanApplicationModalBtn.onclick = function () {
                            openModal(loanApplicationModal); // Pass intended modal
                        };

                        openHealthInsuranceModalBtn.onclick = function () {
                            openModal(healthInsuranceModal); // Pass intended modal
                        };

                        // Close modals
                        closeLoanApplicationModal.onclick = function () {
                            loanApplicationModal.style.display = 'none';
                        };

                        closeHealthInsuranceModal.onclick = function () {
                            healthInsuranceModal.style.display = 'none';
                        };

                        closeVerificationModal.onclick = function () {
                            verificationModal.style.display = 'none';
                        };

                        closeEligibilityModal.onclick = function () {
                            eligibilityModal.style.display = 'none';
                        };
                        closeContributionModal.onclick = function () {
                            contributionModal.style.display = 'none';
                        };

                        // Close modals if user clicks outside of them
                        window.onclick = function (event) {
                            if (event.target === loanApplicationModal) {
                                loanApplicationModal.style.display = 'none';
                            } else if (event.target === healthInsuranceModal) {
                                healthInsuranceModal.style.display = 'none';
                            } else if (event.target === savingsApplicationModal) {
                                savingsApplicationModal.style.display = 'none';
                            } else if (event.target === verificationModal) {
                                verificationModal.style.display = 'none';
                            } else if (event.target === eligibilityModal) {
                                eligibilityModal.style.display = 'none';
                            } else if (event.target === contributionModal) {
                                contributionModal.style.display = 'none';
                            }
                        };
                    });

                </script>

                <?php

                echo "<script>console.log('isVerified: ', " . json_encode($_SESSION['verified'] === 'True') . ");</script>";
                echo "<script>console.log('canApply: ', " . json_encode($canApply) . ");</script>";
                echo "<script>console.log('isContributionEligible: ', " . json_encode($isContributionEligible) . ");</script>";

                ?>
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
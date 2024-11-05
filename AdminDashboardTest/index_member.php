<?php
require 'check_user.php';
include 'connection.php';

// Initialize eligibility variables
$canApply = false;
$isContributionEligibleForLoan = false; // Eligibility for Loan Application Modal
$isContributionEligibleForHealth = false; // Eligibility for Health Insurance Modal

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

            // Check if total contribution meets criteria for loan and health insurance
            if ($totalContribution >= 10000) {
                $isContributionEligibleForLoan = true; // Eligible for loan
            }
            if ($totalContribution >= 660) {
                $isContributionEligibleForHealth = true; // Eligible for health insurance
            }
        } else {
            echo "No contributions found!";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }
}
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


                <!-- Loan Option Explanation Modal -->
                <div id="openLoanOptionModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeOpenLoanOptionModal">&times;</span>
                        <h2>Loan Eligibility Based on Contribution</h2>
                        <p>Your loan amount eligibility is determined by your contribution amount:</p>
                        <ul>
                            <li>If your contribution is between <strong>10,000 and 20,000</strong>, you can loan up to
                                <strong>30,000</strong>.
                            </li>
                            <li>If your contribution is between <strong>20,000 and 30,000</strong>, you can loan up to
                                <strong>60,000</strong>.
                            </li>
                            <li>If your contribution is between <strong>30,000 and 40,000</strong>, you can loan up to
                                <strong>90,000</strong>.
                            </li>
                            <li>If your contribution is between <strong>40,000 and 50,000</strong>, you can loan up to
                                <strong>120,000</strong>.
                            </li>
                            <li>If your contribution is between <strong>50,000 and 60,000</strong>, you can loan up to
                                <strong>150,000</strong>.
                            </li>
                            <li>If your contribution is between <strong>60,000 and 70,000</strong>, you can loan up to
                                <strong>180,000</strong>.
                            </li>
                            <li>If your contribution is between <strong>70,000 and 80,000</strong>, you can loan up to
                                <strong>210,000</strong>.
                            </li>
                            <li>If your contribution is between <strong>80,000 and 90,000</strong>, you can loan up to
                                <strong>240,000</strong>.
                            </li>
                            <li>If your contribution is <strong>90,000 or more</strong>, you can loan up to
                                <strong>270,000</strong>.
                            </li>
                        </ul>
                    </div>
                </div>


                <div id="reviewModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeReviewModal">&times;</span>
                        <h2>Review Your Application</h2>
                        <div id="reviewContent"></div> <!-- This will be populated with reviewContent -->
                        <button id="confirmSubmitBtn">Confirm Submission</button>
                    </div>
                </div>



                <!-- Loan Modal -->
                <div id="loanApplicationModal" class="modal">
                    <div class="modal-content">
                        <h2>Loan Application</h2>
                        <div class="loan-option-container" style="text-align: right; size: 10px;">

                            <button>
                                <span class="fa fa-question" id="openLoanOption"></span>
                            </button>

                            <button style="background-color: red;">
                                <span class="fa fa-window-close" id="closeLoanApplicationModal"></span>
                            </button>


                        </div>

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

                            <label for="bank_name">Bank Name:</label>
                            <input type="text" id="bank_name" name="bank_name" required><br>

                            <label for="bank_id">Bank ID:</label>
                            <input type="text" id="bank_id" name="bank_id" required><br>

                            <label for="branch">Branch:</label>
                            <input type="text" id="branch" name="branch" required><br>


                            <label for="applicantIncome">Salary:</label>
                            <input type="number" id="applicantIncome" name="annual_income"
                                value="<?php echo isset($_SESSION['member_salary']) ? htmlspecialchars($_SESSION['member_salary']) : ''; ?>"
                                readonly required><br>

                            <label for="loanAmount">Loan Amount Requested:</label>
                            <input type="number" id="loanAmount" name="loan_amount" step="0.01" required><br>

                            <label for="loanTerm">Loan Term:</label>
                            <select id="loanTerm" name="loan_term" required>
                                <option value="1">Short Term</option>
                                <option value="3">Mid Term</option>
                                <option value="5">Long Term</option>
                            </select><br>

                            <label for="loanPurpose">Loan Type:</label>
                            <select id="loanPurpose" name="loan_purpose" required>
                                <option value="Regular">Regular</option>
                                <option value="Fidelity">Fidelity</option>
                                <option value="LBP">LBP Loan Assisted Livelihood Program</option>
                                <option value="Personal">Personal</option>
                                <option value="Business">Business</option>
                            </select><br>

                            <label for="employmentStatus">Employment Status:</label>
                            <select id="employmentStatus" name="employment_status" required>
                                <option value="Employed">Employed</option>
                                <option value="Self-Employed">Self-Employed</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Retired">Retired</option>
                            </select><br>

                            <!-- Collateral Checkbox -->
                            <label for="useCollateral">Use Collateral: <input type="checkbox" id="useCollateral"
                                    name="use_collateral" onchange="toggleCollateralFields()"></label>

                            <!-- Collateral Fields (Initially Hidden) -->
                            <div id="collateralFields" style="display: none;">
                                <label for="collateral">Collateral:</label>
                                <input type="text" id="collateral" name="collateral"><br>

                                <label for="collateral_image">Collateral Image:</label>
                                <input type="file" id="collateral_image" name="collateral_image" accept="image/*"><br>
                            </div>

                            <script>
                                function toggleCollateralFields() {
                                    const checkbox = document.getElementById('useCollateral');
                                    const collateralFields = document.getElementById('collateralFields');
                                    collateralFields.style.display = checkbox.checked ? 'block' : 'none';
                                }
                            </script>

                            <label for="paymentPlan">Payment Plan:</label>
                            <select id="paymentPlan" name="payment_plan" required>
                                <option value="">Select Payment Plan</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annually">Annually</option>
                            </select><br>

                            <div id="comakersSection">
                                <label>Co-Maker's Name:</label>
                                <div class="comaker-entry">
                                    <input type="text" name="comakers_name[]" class="comakers_name" required>
                                    <button type="button" class="remove-comaker" style="display:none;">Remove</button>
                                </div>
                            </div>
                            <button type="button" id="addComakerBtn">Add Another Co-Maker</button><br>

                            <label for="supportingDocument1">Co-Makers Statement document (Image):</label>
                            <input type="file" id="supportingDocument1" name="supporting_document_1" accept="image/*"
                                required><br>

                            <label for="supportingDocument2">Deed of Assignment document (Image):</label>
                            <input type="file" id="supportingDocument2" name="supporting_document_2"
                                accept="image/*"><br>

                            <button id="loanSubmitBtn">Submit Application</button>
                        </form>
                    </div>
                </div>


                <!-- Health Modal -->
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

                <script src="reviewModal.js"></script>
                <script src="comakers_script.js"></script>


                <?php
                // Initialize eligibility variables
                $canApply = false;
                $isContributionEligibleForLoan = false; // Eligibility for Loan Application Modal
                $isContributionEligibleForHealth = false; // Eligibility for Health Insurance Modal
                
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

                            // Check if total contribution meets criteria for loan and health insurance
                            if ($totalContribution >= 10000) {
                                $isContributionEligibleForLoan = true; // Eligible for loan
                            }
                            if ($totalContribution >= 660) {
                                $isContributionEligibleForHealth = true; // Eligible for health insurance
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

                <div id="contributionErrorModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" id="closecontributionErrorModal">&times;</span>
                        <h2>Eligibility Information</h2>
                        <p>Your total contributions must exceed 10,000 PHP to apply.</p>
                    </div>
                </div>

                <div id="contributionErrorModalHealth" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" id="closecontributionErrorModalHealth">&times;</span>
                        <h2>Eligibility Information</h2>
                        <p>Your total contributions must exceed 660 PHP to apply.</p>
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
                        const contributionErrorModal = document.getElementById('contributionErrorModal');
                        const contributionErrorModalHealth = document.getElementById('contributionErrorModalHealth');
                        // Open buttons
                        const openLoanApplicationModalBtn = document.getElementById('openLoanApplicationModalBtn');
                        const openHealthInsuranceModalBtn = document.getElementById('openHealthInsuranceModalBtn');

                        // Close buttons
                        const closeLoanApplicationModal = document.getElementById('closeLoanApplicationModal');
                        const closeHealthInsuranceModal = document.getElementById('closeHealthInsuranceModal');
                        const closeSavingsApplicationModal = document.getElementById('closeSavingsApplicationModal');
                        const closeVerificationModal = document.getElementById('closeVerificationModal');
                        const closeEligibilityModal = document.getElementById('closeEligibilityModal');
                        const closecontributionErrorModal = document.getElementById('closecontributionErrorModal');
                        const closecontributionErrorModalHealth = document.getElementById('closecontributionErrorModalHealth');
                        // Pass server-side variables to JavaScript
                        const isVerified = <?php echo json_encode($_SESSION['verified'] === 'True'); ?>; // Pass verified status to JS
                        const canApply = <?php echo json_encode($canApply); ?>; // Pass eligibility to JS
                        const isContributionEligibleForLoan = <?php echo json_encode($isContributionEligibleForLoan); ?>; // Loan contribution eligibility
                        const isContributionEligibleForHealth = <?php echo json_encode($isContributionEligibleForHealth); ?>; // Health contribution eligibility

                        // Function to open the loan application modal with verification and eligibility checks
                        function openLoanModal() {
                            if (!isVerified) {
                                verificationModal.style.display = 'block';
                            } else if (!canApply) {
                                eligibilityModal.style.display = 'block';
                            } else if (!isContributionEligibleForLoan) {
                                contributionErrorModal.style.display = 'block'; // Show contribution modal if not eligible for loan
                            } else {
                                loanApplicationModal.style.display = 'block'; // Show loan application modal if all conditions met
                            }
                        }

                        // Function to open the health insurance modal with verification and eligibility checks
                        function openHealthModal() {
                            if (!isVerified) {
                                verificationModal.style.display = 'block';
                            } else if (!canApply) {
                                eligibilityModal.style.display = 'block';
                            } else if (!isContributionEligibleForHealth) {
                                contributionErrorModalHealth.style.display = 'block'; // Show contribution modal if not eligible for health insurance
                            }
                            else {
                                healthInsuranceModal.style.display = 'block'; // Show health insurance modal if all conditions met
                            }
                        }

                        // Set onclick events for opening modals
                        openLoanApplicationModalBtn.onclick = openLoanModal;
                        openHealthInsuranceModalBtn.onclick = openHealthModal;

                        // Close modal events
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
                        closecontributionErrorModal.onclick = function () {
                            contributionErrorModal.style.display = 'none';
                        };
                        closecontributionErrorModalHealth.onclick = function () {
                            contributionErrorModalHealth.style.display = 'none';
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
                            } else if (event.target === contributionErrorModal) {
                                contributionErrorModal.style.display = 'none';
                            } else if (event.target === contributionErrorModalHealth) {
                                contributionErrorModalHealth.style.display = 'none';
                            }
                        };
                    });
                </script>


                <?php

                echo "<script>console.log('isVerified: ', " . json_encode($_SESSION['verified'] === 'True') . ");</script>";
                echo "<script>console.log('canApply: ', " . json_encode($canApply) . ");</script>";
                echo "<script>console.log('isContributionEligible: ', " . json_encode($isContributionEligibleForHealth) . ");</script>";
                echo "<script>console.log('isContributionEligible: ', " . json_encode($isContributionEligibleForLoan) . ");</script>";
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
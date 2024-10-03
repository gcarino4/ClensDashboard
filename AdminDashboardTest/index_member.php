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
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/application.css">

    <script src="index.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>CoLens Dashboard</title>
    <script>
        // Set the isVerified variable based on the PHP session
        const isVerified = <?php echo json_encode($_SESSION['verified'] === 'True'); ?>;
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

                <button id="openSavingsApplicationModalBtn" class="btn-large centered" data-action="loan-application">
                    <i class='fas fa-money-bill-wave'></i> Apply for Savings
                </button>

                <?php
                include 'approved_loans.php';
                echo "<br>";
                include 'approved_health_insurance.php';
                echo "<br>";
                include 'loan_payments_table.php';
                echo "<br>";
                include 'health_payments_table.php';
                ?>

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

                            <label for="applicantIncome">Annual Income:</label>
                            <input type="number" id="applicantIncome" name="annual_income" step="0.01" required><br>

                            <label for="loanAmount">Loan Amount Requested:</label>
                            <input type="number" id="loanAmount" name="loan_amount" step="0.01" required><br>

                            <label for="loanTerm">Loan Term (in years):</label>
                            <input type="number" id="loanTerm" name="loan_term" min="1" max="30" required><br>

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

                            <!-- Payment Plan Dropdown -->
                            <label for="paymentPlan">Payment Plan:</label>
                            <select id="paymentPlan" name="payment_plan" required>
                                <option value="">Select Payment Plan</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annually">Annually</option>
                            </select><br>

                            <!-- Add File Uploads for Supporting Documents -->
                            <label for="supportingDocument1">Supporting Document 1 (Image):</label>
                            <input type="file" id="supportingDocument1" name="supporting_document_1" accept="image/*"
                                required><br>

                            <label for="supportingDocument2">Supporting Document 2 (Image):</label>
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
                                    <input type="text" name="beneficiary_name[]" required><br>

                                    <label for="beneficiaryRelationship">Relationship:</label>
                                    <input type="text" name="beneficiary_relationship[]" required><br>

                                    <label for="beneficiaryDOB">Date of Birth:</label>
                                    <input type="date" name="beneficiary_dob[]" required><br>
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

                <!-- JavaScript to add more beneficiaries -->
                <script>
                    document.getElementById('addBeneficiaryBtn').addEventListener('click', function () {
                        const beneficiariesDiv = document.getElementById('beneficiaries');
                        const newBeneficiaryIndex = beneficiariesDiv.getElementsByClassName('beneficiary').length + 1;

                        const newBeneficiaryDiv = document.createElement('div');
                        newBeneficiaryDiv.classList.add('beneficiary');
                        newBeneficiaryDiv.innerHTML = `
        <label for="beneficiaryName${newBeneficiaryIndex}">Beneficiary Name ${newBeneficiaryIndex}:</label>
        <input type="text" name="beneficiary_name[]" required><br>

        <label for="beneficiaryRelationship${newBeneficiaryIndex}">Relationship ${newBeneficiaryIndex}:</label>
        <input type="text" name="beneficiary_relationship[]" required><br>

        <label for="beneficiaryDOB${newBeneficiaryIndex}">Date of Birth ${newBeneficiaryIndex}:</label>
        <input type="date" name="beneficiary_dob[]" required><br>
    `;

                        beneficiariesDiv.appendChild(newBeneficiaryDiv);
                    });
                </script>

                <!-- Style for the Modal (optional) -->
                <style>
                    .modal {
                        display: none;
                        position: fixed;
                        z-index: 1;
                        padding-top: 60px;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgb(0, 0, 0);
                        background-color: rgba(0, 0, 0, 0.4);
                    }

                    .modal-content {
                        background-color: #fefefe;
                        margin: 5% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                    }

                    .close {
                        color: #aaa;
                        float: right;
                        font-size: 28px;
                        font-weight: bold;
                    }

                    .close:hover,
                    .close:focus {
                        color: black;
                        text-decoration: none;
                        cursor: pointer;
                    }
                </style>



                <div id="savingsApplicationModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeSavingsApplicationModal">&times;</span>

                        <h2>Savings Application</h2>
                        <form id="savingsApplicationForm" method="post" action="submit_savings_application.php">
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

                            <label for="savingsAmount">Savings Amount:</label>
                            <input type="number" id="savingsAmount" name="savings_amount" step="0.01" required><br>

                            <label for="interestRate">Interest Rate (Annual %):</label>
                            <input type="number" id="interestRate" name="interest_rate" step="0.01" required><br>

                            <label for="savingsTerm">Savings Term (in years):</label>
                            <input type="number" id="savingsTerm" name="savings_term" min="1" max="30" required><br>

                            <label for="savingsPurpose">Purpose of Savings:</label>
                            <select id="savingsPurpose" name="savings_purpose" required>
                                <option value="Retirement">Retirement</option>
                                <option value="Education">Education</option>
                                <option value="Emergency Fund">Emergency Fund</option>
                                <option value="Vacation">Vacation</option>
                                <option value="Other">Other</option>
                            </select><br>

                            <label for="savingsAccountType">Savings Account Type:</label>
                            <select id="savingsAccountType" name="savings_account_type" required>
                                <option value="Fixed Deposit">Fixed Deposit</option>
                                <option value="Recurring Deposit">Recurring Deposit</option>
                                <option value="Savings Account">Savings Account</option>
                            </select><br>

                            <!-- Payment Plan Dropdown (e.g., for recurring deposits) -->
                            <label for="paymentPlan">Deposit Frequency:</label>
                            <select id="paymentPlan" name="payment_plan" required>
                                <option value="">Select Frequency</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annually">Annually</option>
                            </select><br>

                            <button type="submit" id="savingsSubmitBtn">Submit Savings Application</button>
                        </form>
                    </div>
                </div>

                <!-- Modal for verification notice -->
                <div id="verificationModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
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

                <div id="savingsApplicationModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeSavingsApplicationModal">&times;</span>
                        <h2>Health Insurance Application</h2>
                        <form id="savingsApplicationForm" method="post" action="">
                            <!-- Your form fields -->
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Modal elements
                        const loanApplicationModal = document.getElementById('loanApplicationModal');
                        const healthInsuranceModal = document.getElementById('healthInsuranceModal');
                        const savingsApplicationModal = document.getElementById('savingsApplicationModal');
                        const verificationModal = document.getElementById('verificationModal');

                        // Open buttons
                        const openLoanApplicationModalBtn = document.getElementById('openLoanApplicationModalBtn');
                        const openHealthInsuranceModalBtn = document.getElementById('openHealthInsuranceModalBtn');
                        const openSavingsApplicationModalBtn = document.getElementById('openSavingsApplicationModalBtn');

                        // Close buttons
                        const closeLoanApplicationModal = document.getElementById('closeLoanApplicationModal');
                        const closeHealthInsuranceModal = document.getElementById('closeHealthInsuranceModal');
                        const closeSavingsApplicationModal = document.getElementById('closeSavingsApplicationModal');
                        const closeVerificationModal = document.querySelector('.close-btn');

                        // Function to open the modal if verified
                        function openModal(modal) {
                            if (isVerified) {
                                modal.style.display = 'block';
                            } else {
                                verificationModal.style.display = 'block';
                            }
                        }

                        // Open modals based on verification
                        openLoanApplicationModalBtn.onclick = function () {
                            openModal(loanApplicationModal);
                        };

                        openHealthInsuranceModalBtn.onclick = function () {
                            openModal(healthInsuranceModal);
                        };

                        openSavingsApplicationModalBtn.onclick = function () {
                            openModal(savingsApplicationModal);
                        };

                        // Close modals
                        closeLoanApplicationModal.onclick = function () {
                            loanApplicationModal.style.display = 'none';
                        };

                        closeHealthInsuranceModal.onclick = function () {
                            healthInsuranceModal.style.display = 'none';
                        };

                        closeSavingsApplicationModal.onclick = function () {
                            savingsApplicationModal.style.display = 'none';
                        };

                        closeVerificationModal.onclick = function () {
                            verificationModal.style.display = 'none';
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
                            }
                        };
                    });
                </script>

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
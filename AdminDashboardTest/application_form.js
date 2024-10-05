document.addEventListener('DOMContentLoaded', function () {
    // Modal elements
    const loanApplicationModal = document.getElementById('loanApplicationModal');
    const healthInsuranceModal = document.getElementById('healthInsuranceModal');
    const savingsApplicationModal = document.getElementById('savingsApplicationModal');
    const verificationModal = document.getElementById('verificationModal');
    const eligibilityModal = document.getElementById('eligibilityModal');

    // Open buttons
    const openLoanApplicationModalBtn = document.getElementById('openLoanApplicationModalBtn');
    const openHealthInsuranceModalBtn = document.getElementById('openHealthInsuranceModalBtn');

    // Close buttons
    const closeLoanApplicationModal = document.getElementById('closeLoanApplicationModal');
    const closeHealthInsuranceModal = document.getElementById('closeHealthInsuranceModal');
    const closeSavingsApplicationModal = document.getElementById('closeSavingsApplicationModal');
    const closeVerificationModal = document.getElementById('closeVerificationModal');
    const closeEligibilityModal = document.getElementById('closeEligibilityModal');

    // Assuming isVerified, canApply, and isContributionEligible variables are set from the server-side PHP code
    const isVerified = <?php echo json_encode($_SESSION['verified']); ?>; // Pass verified status to JS
    const canApply = <?php echo json_encode($canApply); ?>; // Pass eligibility to JS
    const isContributionEligible = <?php echo json_encode($isContributionEligible); ?>; // Pass contribution eligibility to JS

    // Function to open the modal based on verification, eligibility, and contribution status
    function openModal(modal) {
        if (!isVerified) {
            verificationModal.style.display = 'block'; // Show verification modal if not verified
        } else if (!canApply) {
            eligibilityModal.style.display = 'block'; // Show eligibility modal if not eligible
        } else if (!isContributionEligible) {
            eligibilityModal.style.display = 'block'; // Show eligibility modal if contribution is not eligible
        } else {
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
        }
    };
});

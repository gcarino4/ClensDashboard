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

    // Function to open the modal based on verification and eligibility
    function openModal(modal, eligibility) {
        if (!isVerified && !eligibility) {
            verificationModal.style.display = 'block'; // Show verification modal if not verified
        } else if (!isVerified) {
            verificationModal.style.display = 'block'; // Show verification modal if not verified
        } else if (!eligibility) {
            eligibilityModal.style.display = 'block'; // Show eligibility modal if not eligible
        } else {
            modal.style.display = 'block'; // Show the intended modal if both verified and eligible
        }
    }

    // Open modals based on verification and eligibility
    openLoanApplicationModalBtn.onclick = function () {
        openModal(loanApplicationModal, canApply); // Pass canApply status
    };

    openHealthInsuranceModalBtn.onclick = function () {
        openModal(healthInsuranceModal, canApply); // Pass canApply status
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

document.getElementById('loanSubmitBtn').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent form submission

    document.getElementById('loanApplicationModal').style.display = "none";

    // Collect data from the form
    const memberId = document.getElementById('memberId').value;
    const applicantName = document.getElementById('applicantName').value;
    const applicantEmail = document.getElementById('applicantEmail').value;
    const applicantPhone = document.getElementById('applicantPhone').value;
    const applicantAddress = document.getElementById('applicantAddress').value;
    const bankInfo = document.getElementById('bank_info').value;
    const applicantIncome = document.getElementById('applicantIncome').value;
    const loanAmount = document.getElementById('loanAmount').value;
    const loanTerm = document.getElementById('loanTerm').value;
    const loanPurpose = document.getElementById('loanPurpose').value;
    const employmentStatus = document.getElementById('employmentStatus').value;
    const useCollateral = document.getElementById('useCollateral').checked ? "Yes" : "No";
    const collateral = document.getElementById('collateral').value;
    const paymentPlan = document.getElementById('paymentPlan').value;

    // Calculate interest rate based on loan term
let interestRate = 0;
if (loanTerm == 1) {
    interestRate = 0.02; // 2% for 6 months - 1 year
} else if (loanTerm == 3) {
    interestRate = 0.06; // 6% for 1 year - 3 years
} else if (loanTerm == 5) {
    interestRate = 0.10; // 10% for 3 years - 5 years
}

// Calculate total interest
const interest = loanAmount * interestRate;

// Calculate the number of dates to pay based on payment plan
let datesToPay = 0;
if (paymentPlan === "monthly") {
    datesToPay = loanTerm * 12; // 12 months in a year
} else if (paymentPlan === "quarterly") {
    datesToPay = loanTerm * 4; // 4 quarters in a year
} else if (paymentPlan === "annually") {
    datesToPay = loanTerm; // Pay once a year
}

// Calculate total amount to be paid
const amountToBePaid = (parseFloat(loanAmount) + interest) / datesToPay;

// Construct review content
const reviewContent = `
    <p><strong>Member ID:</strong> ${memberId}</p>
    <p><strong>Name:</strong> ${applicantName}</p>
    <p><strong>Email:</strong> ${applicantEmail}</p>
    <p><strong>Phone Number:</strong> ${applicantPhone}</p>
    <p><strong>Address:</strong> ${applicantAddress}</p>
    <p><strong>Bank Details:</strong> ${bankInfo}</p>
    <p><strong>Annual Income:</strong> ${applicantIncome}</p>
    <p><strong>Loan Amount Requested:</strong> ${loanAmount}</p>
    <p><strong>Loan Term:</strong> ${loanTerm}</p>
    <p><strong>Purpose of Loan:</strong> ${loanPurpose}</p>
    <p><strong>Employment Status:</strong> ${employmentStatus}</p>
    <p><strong>Use Collateral:</strong> ${useCollateral}</p>
    ${useCollateral === "Yes" ? `<p><strong>Collateral:</strong> ${collateral}</p>` : ""}
    <p><strong>Payment Plan:</strong> ${paymentPlan}</p>
    <p><strong>Interest Rate:</strong> ${interestRate * 100}%</p>
    <p><strong>Total Interest:</strong> ${interest.toFixed(2)}</p>
    <p><strong>Dates to Pay:</strong> ${datesToPay}</p>
    <p><strong>Payment Due:</strong> ${amountToBePaid.toFixed(2)}</p>
`;


    // Populate the review content and show the modal
    document.getElementById('reviewContent').innerHTML = reviewContent;
    document.getElementById('reviewModal').style.display = "block";
});

// Close review modal
document.getElementById('closeReviewModal').addEventListener('click', function() {
    document.getElementById('reviewModal').style.display = "none";
});



// Confirm submission
document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
    document.getElementById('loanApplicationForm').submit(); // Submit the form
});

// Get the modal
var modal = document.getElementById("openLoanOptionModal");

// Get the button that opens the modal
var btn = document.getElementById("openLoanOption");

// Get the <span> element that closes the modal
var span = document.getElementById("closeOpenLoanOptionModal");

// When the user clicks the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
    document.getElementById('loanApplicationModal').style.display = "none";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
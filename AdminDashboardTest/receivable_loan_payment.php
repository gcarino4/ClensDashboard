<form id="searchForm">
    <label for="searchMemberId">Member ID:</label>
    <input type="text" id="searchMemberId" name="searchMemberId">

    <label for="searchApplicationId">Application ID:</label>
    <input type="text" id="searchApplicationId" name="searchApplicationId">

    <button type="submit">Search</button>
</form>

<!-- Search Result -->
<div id="searchResult" style="display:none;">
    <h3>Loan Details</h3>
    <p>Application ID: <span id="resultApplicationId"></span></p>
    <p>Member ID: <span id="resultMemberId"></span></p>
    <p>Loan Amount: <span id="resultLoanAmount"></span></p>
</div>

<script>

    document.getElementById('searchForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const memberId = document.getElementById('searchMemberId').value;
        const applicationId = document.getElementById('searchApplicationId').value;

        // Send the search data to the server via fetch
        fetch('loan_search.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                member_id: memberId,
                application_id: applicationId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display the search result in the form
                    document.getElementById('resultApplicationId').textContent = data.application_id;
                    document.getElementById('resultMemberId').textContent = data.member_id;
                    document.getElementById('resultLoanAmount').textContent = data.loan_amount;

                    // Set the values in the payment modal
                    document.getElementById('modalApplicationId').value = data.application_id;
                    document.getElementById('modalMemberId').value = data.member_id;
                    document.getElementById('paymentAmount').setAttribute('max', data.loan_amount);

                    // Show the loan details and enable payment form
                    document.getElementById('searchResult').style.display = 'block';
                } else {
                    alert('No loan found for the provided details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });


</script>
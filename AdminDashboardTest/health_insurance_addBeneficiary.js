document.getElementById('addBeneficiaryBtn').addEventListener('click', function() {
    // Create a new div for the beneficiary fields
    const newBeneficiary = document.createElement('div');
    newBeneficiary.classList.add('beneficiary');
    
    // Add the new fields for beneficiary
    newBeneficiary.innerHTML = `
        <label for="beneficiaryName">Beneficiary Name:</label>
        <input type="text" name="beneficiary_name[]" required><br>

        <label for="beneficiaryRelationship">Relationship:</label>
        <input type="text" name="beneficiary_relationship[]" required><br>

        <label for="beneficiaryDOB">Date of Birth:</label>
        <input type="date" name="beneficiary_dob[]" required><br>
    `;

    // Append the new beneficiary fields to the form
    document.getElementById('beneficiaries').appendChild(newBeneficiary);
});
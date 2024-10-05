
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


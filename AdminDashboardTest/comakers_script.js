
// Script to handle adding multiple Co-Maker's Name fields
document.addEventListener('DOMContentLoaded', function() {
    const comakersSection = document.getElementById('comakersSection');
    const addComakerBtn = document.getElementById('addComakerBtn');

    // Add a new Co-Maker's Name field
    addComakerBtn.addEventListener('click', function() {
        const newComaker = document.createElement('div');
        newComaker.classList.add('comaker-entry');
        newComaker.innerHTML = `
            <input type="text" name="comakers_name[]" class="comakers_name" required>
            <button type="button" class="remove-comaker">Remove</button>
        `;
        comakersSection.appendChild(newComaker);

        // Show the "Remove" button when there are multiple Co-Makers
        const removeButtons = document.querySelectorAll('.remove-comaker');
        removeButtons.forEach(button => {
            button.style.display = 'inline-block';
        });
    });

    // Remove a Co-Maker's Name field
    comakersSection.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-comaker')) {
            event.target.parentElement.remove();

            // Hide "Remove" buttons if only one Co-Maker field is left
            const removeButtons = document.querySelectorAll('.remove-comaker');
            if (removeButtons.length === 1) {
                removeButtons[0].style.display = 'none';
            }
        }
    });
});

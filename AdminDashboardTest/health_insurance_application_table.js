
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.approveBtn').forEach(button => {
        button.addEventListener('click', function() {
            const applicationId = this.getAttribute('data-id');
            updateStatus(applicationId, 'approved');
        });
    });

    document.querySelectorAll('.rejectBtn').forEach(button => {
        button.addEventListener('click', function() {
            const applicationId = this.getAttribute('data-id');
            updateStatus(applicationId, 'rejected');
        });
    });

    function updateStatus(applicationId, status) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_health_insurance_application_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log('AJAX request completed. Status:', xhr.status); // Debug log
                console.log('Response from server:', xhr.responseText); // Debug log
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText); // Parse JSON response
                    if (response.success) {
                        alert('Status updated successfully!'); // Success alert
                        location.reload(); // Reload the page to see changes
                    } else {
                        alert('Failed to update status: ' + response.message); // Show error message from server
                    }
                } else {
                    alert('An error occurred while processing your request.'); // Error alert for HTTP status
                }
            }
        };
        xhr.send('application_id=' + encodeURIComponent(applicationId) + '&status=' + encodeURIComponent(status));
    }
    

});

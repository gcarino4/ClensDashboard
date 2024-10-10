
                document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.approveBtn').forEach(button => {
        button.addEventListener('click', function() {
            const applicationId = this.getAttribute('data-id');
            console.log('Approve button clicked for ID:', applicationId); // Debug log
            updateStatus(applicationId, 'approved');
            
        });
    });

    document.querySelectorAll('.rejectBtn').forEach(button => {
        button.addEventListener('click', function() {
            const applicationId = this.getAttribute('data-id');
            console.log('Reject button clicked for ID:', applicationId); // Debug log
            updateStatus(applicationId, 'rejected');
        });
    });


                    function updateStatus(applicationId, status) {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', 'update_loan_application_status.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                if (xhr.responseText === 'success') {
                                    location.reload(); // Reload the page to see changes
                                } else {
                                    alert('Failed to update status.');
                                }
                            }
                        };
                        xhr.send('application_id=' + encodeURIComponent(applicationId) + '&status=' + encodeURIComponent(status));
                    }

                    
                });
                
                
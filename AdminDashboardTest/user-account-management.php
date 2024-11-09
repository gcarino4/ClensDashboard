<?php

include 'check_user.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/user-account-form.css">
  <link rel="stylesheet" href="css/user_account_management.css">
  <title>User Account Management</title>


  <style>
    .hidden {
      ` display: none;
    }

    .passwordModal {
      display: none;
      /* Hidden by default */
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .passwordModal-modal-content {
      background-color: #fefefe;
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 30%;
    }

    .closePasswordModal {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
  </style>


</head>

<body>

  <div class="container">
    <!-- Sidebar Section -->
    <?php
    include 'sidebar.php';
    ?>
    <!-- End of Sidebar Section -->

    <!-- Main Content -->

    <main>
      <h1>User Account Management</h1>
      <br>
      <br>
      <!-- Analyses -->

      <?php
      include 'new_users.php';
      ?>

      <div class="btn-text-left">

        <!--
        <button onclick="showCreateAccountModal()" class="btn">Add User</button>
      -->

        <div id="passwordModal" class="passwordModal">
          <div class="passwordModal-modal-content">
            <span class="closePasswordModal">&times;</span>
            <h2>Update Password</h2>
            <form id="passwordUpdateForm">
              <input type="hidden" name="member_id" id="modal_member_id" value="">
              <label for="new_password">New Password:</label>
              <input type="password" name="new_password" id="new_password" required>
              <label for="confirm_password">Confirm Password:</label>
              <input type="password" name="confirm_password" id="confirm_password" required>
              <button type="submit">Update Password</button>
            </form>
          </div>
        </div>

        <div id="create-account-modal" class="modal"><br><br>
          <div class="modal-content">
            <span class="close" id="closeCreateAccountModal">&times;</span>

            <h2>Add User Information</h2><br>

            <form method="post" id="create-account-form" action="add_new_user.php">

              <div class="form-group">
                <input type="hidden" id="member_id" name="member_id">
                <input type="hidden" id="age" name="age"> <!-- Hidden age field -->
              </div><br>

              <div class="form-group">
                <label for="username" class="form-label">Name:</label>
                <input type="text" id="username" name="username" class="form-control" required>
              </div><br>

              <div class="form-group">
                <label for="contact_no" class="form-label">Contact Number:</label>
                <input type="text" id="contact_no" name="contact_no" class="form-control" required>
              </div><br>

              <div class="form-group">
                <label for="birthday" class="form-label">Birthday:</label>
                <input type="date" id="birthday" name="birthday" class="form-control" required>
              </div><br>

              <div class="form-group">
                <label for="sex" class="form-label">Sex:</label>
                <select id="sex" name="sex" class="form-control" required>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Female">Other</option>
                </select>
              </div><br>

              <div class="form-group hidden" id="otherSexDiv">
                <label for="civil_status" class="form-label">Civil Status:</label>
                <select id="civil_status" name="civil_status" class="form-control" required>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Divorced">Divorced</option>
                  <option value="Widowed">Widowed</option>
                </select>
              </div><br>

              <div class="form-group">
                <label for="address" class="form-label">Address:</label>
                <input type="text" id="address" name="address" class="form-control" required>
              </div><br>

              <div class="form-group">
                <label for="address" class="form-label">Email:</label>
                <input type="text" id="email" name="email" class="form-control" required>
              </div><br>

              <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
              </div><br>

              <div class="form-group">
                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                <div id="passwordFeedback" class="form-text text-danger mt-2" style="display: none;">
                  Passwords do not match.
                </div>
              </div><br>

              <div class="form-group">
                <label for="verified">Verified:</label>
                <select id="verified" name="verified" required>
                  <option value="">Select</option>
                  <option value="true">True</option>
                  <option value="false">False</option>
                </select>
              </div><br>

              <button type="submit" class="btn">Add User</button>
              <br>

            </form>

            <script>
              function showOtherInput(selectElement) {
                var otherSexDiv = document.getElementById('otherSexDiv');
                if (selectElement.value === 'other') {
                  otherSexDiv.classList.remove('hidden');
                } else {
                  otherSexDiv.classList.add('hidden');
                }
              }
            </script>




          </div>
        </div>



      </div>
      <br>
      <?php
      include 'user-account-management-table.php';
      ?>
    </main>

    <div id="editModal" class="modal"><br><br>
      <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>

        <h2>Update User</h2><br>
        <form id="editForm" method="post">
          <input type="hidden" id="editId" name="id">

          <label for="editName">Name:</label>
          <input type="text" id="editName" name="name"><br>

          <label for="editBirthday">Birthday:</label>
          <input type="date" id="editBirthday" name="birthday"><br>

          <input type="hidden" id="editAge" name="age" readonly><br>

          <label for="editSex">Sex:</label>
          <select id="editSex" name="sex">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select><br>

          <label for="editStatus">Status:</label>
          <select id="editStatus" name="status">
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Divorced">Divorced</option>
            <option value="Widowed">Widowed</option>
          </select><br>

          <label for="editAddress">Address:</label>
          <input type="text" id="editAddress" name="address"><br>

          <label for="editContactNumber">Contact Number:</label>
          <input type="text" id="editContactNumber" name="contact_number"><br>

          <label for="editRole">Role:</label>
          <select id="editRole" name="role">
            <option value="Admin">Admin</option>
            <option value="Member">Member</option>
            <option value="Admin Officer">Admin Officer</option>
            <option value="Finance Officer">Finance Officer</option>
          </select><br>

          <label for="editDateOfCreation">Date of Creation:</label>
          <input type="date" id="editDateOfCreation" name="date_of_creation"><br>


          <label for="editVerified">Verified:</label>
          <input type="hidden" name="verified" value="False">
          <!-- This hidden input sends "False" when checkbox is unchecked -->
          <input type="checkbox" id="editVerified" name="verified" value="True"><br>



          <button type="submit" id="editSubmitBtn">Update</button>
          <br>
        </form>


      </div>
    </div>

    <script>
      // Get modal element and close button
      var modal = document.getElementById("passwordModal");
      var closeBtn = document.getElementsByClassName("closePasswordModal")[0];

      // Open modal when "Change Password" button is clicked
      document.querySelectorAll('.passwordBtn').forEach(button => {
        button.addEventListener('click', function () {
          document.getElementById('modal_member_id').value = this.getAttribute('data-id');
          modal.style.display = "block";
        });
      });

      // Close modal when X is clicked
      closeBtn.onclick = function () {
        modal.style.display = "none";
      };

      // Close modal when clicking outside of it
      window.onclick = function (event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      };


      // Handle form submission
      document.getElementById('passwordUpdateForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);

        // AJAX request to update password
        fetch('update_password.php', {
          method: 'POST',
          body: formData
        })
          .then(response => response.text())
          .then(data => {
            alert(data);
            modal.style.display = "none";
            location.reload(); // Refresh to reflect the changes
          })
          .catch(error => console.error('Error:', error));
      });
    </script>

    <script>
      // Function to show the create account modal
      function showCreateAccountModal() {
        document.getElementById('create-account-modal').style.display = 'block';
      }

      // Function to close the create account modal
      function closeCreateAccountModal() {
        document.getElementById('create-account-modal').style.display = 'none';
      }

      // Function to close the edit modal
      function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
      }

      // Event listener for the close button in create account modal
      document.getElementById('closeCreateAccountModal').onclick = closeCreateAccountModal;

      // Event listener for the close button in edit modal
      document.getElementById('closeEditModal').onclick = closeEditModal;

      // Close the modals when clicking outside of them
      window.onclick = function (event) {
        if (event.target == document.getElementById('create-account-modal')) {
          closeCreateAccountModal();
        } else if (event.target == document.getElementById('editModal')) {
          closeEditModal();
        }
      }

      // Ensure both modals are hidden on page load
      window.onload = function () {
        closeCreateAccountModal();
        closeEditModal();
      }

      // Get the edit form
      var editForm = document.getElementById("editForm");

      // Attach a submit event listener to the edit form
      editForm.addEventListener("submit", function (event) {
        // Prevent the default form submission behavior
        event.preventDefault();



        // Get the form data
        var formData = new FormData(editForm);

        // Send an AJAX request to update_record.php
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_record.php");
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              // Reload the page or show a success message
              location.reload();
            } else {
              // Show an error message
              alert("There was an error updating the user record.");
            }
          }
        };
        xhr.send(formData);
      });

      // Function to populate the modal with data when edit button is clicked
      function populateModal(userId, row) {
        document.getElementById("editId").value = userId;
        document.getElementById("editName").value = row.cells[1].textContent;
        document.getElementById("editBirthday").value = row.cells[3].textContent;
        document.getElementById("editAge").value = row.cells[2].textContent;
        document.getElementById("editSex").value = row.cells[4].textContent;
        document.getElementById("editStatus").value = row.cells[5].textContent;
        document.getElementById("editAddress").value = row.cells[6].textContent;
        document.getElementById("editContactNumber").value = row.cells[7].textContent;
        document.getElementById("editRole").value = row.cells[8].textContent;
        var verifiedValue = row.cells[9].textContent.trim();
        document.getElementById("editVerified").checked = (verifiedValue === "True");
        document.getElementById("editDateOfCreation").value = row.cells[11].textContent;

        // Show the modal
        document.getElementById("editModal").style.display = 'block';
      }



      // Get the button that opens the modal
      var editButtons = document.querySelectorAll(".editBtn");

      // Loop through all edit buttons and attach a click event listener
      editButtons.forEach(function (editButton) {
        editButton.addEventListener("click", function () {
          // Get the user's id from the data-id attribute
          var userId = this.getAttribute("data-id");

          // Get the row associated with the edit button
          var row = this.closest("tr");

          // Populate the modal with data
          populateModal(userId, row);
        });
      });

    </script>

    <script>
      document.querySelectorAll('.archiveBtn').forEach(button => {
        button.addEventListener('click', function () {
          const memberId = this.getAttribute('data-id');
          if (confirm('Are you sure you want to delete this member and their contributions?')) {
            // Send the member_id via AJAX to delete the record
            fetch('delete_user.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: 'member_id=' + encodeURIComponent(memberId)
            })
              .then(response => response.text())
              .then(data => {
                alert(data); // Show response message
                if (data.includes('successfully')) {
                  location.reload(); // Reload the page to refresh the table only if successful
                }
              })
              .catch(error => {
                console.error('Error:', error); // Log any errors from the AJAX request
              });
          }
        });
      });
    </script>



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
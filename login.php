<?php
session_start();

$servername = "localhost";
$dbUsername = "root";
$password = "root";
$dbname = "colens";

// Create connection
$conn = new mysqli($servername, $dbUsername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$email = $_POST['username']; // Use email for login
	$password_input = $_POST['password'];

	// Check in the members table (by email)
	$sql = "SELECT * FROM members WHERE email = ?";
	if ($stmt = $conn->prepare($sql)) {
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$hashed_password = $row['password'];

			if (password_verify($password_input, $hashed_password)) {
				$_SESSION['email'] = htmlspecialchars($row['email']);
				$_SESSION['name'] = htmlspecialchars($row['name']);
				$_SESSION['member_id'] = htmlspecialchars($row['member_id']); // Ensure this is set
				$_SESSION['contact_no'] = htmlspecialchars($row['contact_no']); // Ensure this is set
				$_SESSION['address'] = htmlspecialchars($row['address']); // Ensure this is set
				$_SESSION['birthday'] = htmlspecialchars($row['birthday']); // Ensure this is set
				$_SESSION['verified'] = htmlspecialchars($row['verified']); // Ensure this is set
				$_SESSION['date_of_creation'] = htmlspecialchars($row['date_of_creation']); // Ensure this is set
				$_SESSION['loggedin'] = true;
				$_SESSION['member_salary'] = htmlspecialchars($row['member_salary']);
				$_SESSION['role'] = htmlspecialchars($row['role']);


				echo $row['role'];

			} else {
				echo "Incorrect Password!";
			}

		} else {
			echo "No Account Found!";
		}
		$stmt->close();
	} else {
		echo "Error preparing statement.";
	}

	// Close connection
	$conn->close();
}
?>
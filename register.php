<?php
$host = 'localhost';
$dbname = 'colens';
$username = 'root';
$password = 'root';

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $mysqli->real_escape_string($_POST['member_id']);
    $name = $mysqli->real_escape_string($_POST['name']);
    $contact_no = $mysqli->real_escape_string($_POST['contact_no']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $birthday = $mysqli->real_escape_string($_POST['birthday']);
    $age = $mysqli->real_escape_string($_POST['age']);
    $sex = $mysqli->real_escape_string($_POST['sex']);
    $civil_status = $mysqli->real_escape_string($_POST['civil_status']);
    $address = $mysqli->real_escape_string($_POST['address']);
    $place_of_birth = $mysqli->real_escape_string($_POST['place_of_birth']);
    $spouse_name = $mysqli->real_escape_string($_POST['spouse_name']);
    $children = isset($_POST['children']) ? $mysqli->real_escape_string(json_encode($_POST['children'])) : null;
    $education_date = isset($_POST['education_date']) ? $mysqli->real_escape_string(json_encode($_POST['education_date'])) : null;
    $education_school = isset($_POST['education_school']) ? $mysqli->real_escape_string(json_encode($_POST['education_school'])) : null;
    $education_course = isset($_POST['education_course']) ? $mysqli->real_escape_string(json_encode($_POST['education_course'])) : null;
    $employment_date = isset($_POST['employment_date']) ? $mysqli->real_escape_string(json_encode($_POST['employment_date'])) : null;
    $employment_position = isset($_POST['employment_position']) ? $mysqli->real_escape_string(json_encode($_POST['employment_position'])) : null;
    $employment_school = isset($_POST['employment_school']) ? $mysqli->real_escape_string(json_encode($_POST['employment_school'])) : null;
    $cooperative_experience_date = isset($_POST['cooperative_experience_date']) ? $mysqli->real_escape_string(json_encode($_POST['cooperative_experience_date'])) : null;
    $cooperative_experience_position = isset($_POST['cooperative_experience_position']) ? $mysqli->real_escape_string(json_encode($_POST['cooperative_experience_position'])) : null;
    $cooperative_experience_name = isset($_POST['cooperative_experience_name']) ? $mysqli->real_escape_string(json_encode($_POST['cooperative_experience_name'])) : null;
    $training_date = isset($_POST['training_date']) ? $mysqli->real_escape_string(json_encode($_POST['training_date'])) : null;
    $training_course = isset($_POST['training_course']) ? $mysqli->real_escape_string(json_encode($_POST['training_course'])) : null;
    $training_hours = isset($_POST['training_hours']) ? $mysqli->real_escape_string(json_encode($_POST['training_hours'])) : null;
    $business_present = $mysqli->real_escape_string($_POST['business_present']);
    $business_previous = $mysqli->real_escape_string($_POST['business_previous']);
    $affiliation = $mysqli->real_escape_string($_POST['affiliation']);
    $member_salary = $mysqli->real_escape_string($_POST['member_salary']);
    $other_income = $mysqli->real_escape_string($_POST['other_income']);
    $crime = $mysqli->real_escape_string($_POST['crime']);

    // Validate required fields
    $errors = [];
    if (empty($name)) {
        $errors[] = "Full name is required.";
    }
    if (empty($contact_no)) {
        $errors[] = "Contact number is required.";
    }

    if (!preg_match('/^\d{11}$/', $contact_no)) {
        $errors[] = "Contact number must be 11 digits.";
    }

    if (empty($email)) {
        $errors[] = "Email address is required.";
    }
    if (empty($birthday)) {
        $errors[] = "Birthday is required.";
    }
    if (empty($sex)) {
        $errors[] = "Gender is required.";
    }
    if (empty($civil_status)) {
        $errors[] = "Civil status is required.";
    }
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    if (empty($place_of_birth)) {
        $errors[] = "Place of birth is required.";
    }
    if (empty($crime)) {
        $errors[] = "Crime conviction information is required.";
    }

    if (count($errors) > 0) {
        // Handle errors (e.g., display in a modal)
        echo '<script>';
        echo 'document.getElementById("modalErrorMessage").innerText = "' . addslashes(implode('<br>', $errors)) . '";';
        echo 'var errorModal = new bootstrap.Modal(document.getElementById("errorModal"));';
        echo 'errorModal.show();';
        echo '</script>';
        exit();
    }

    // Check if email already exists
    $email_check_sql = "SELECT * FROM members WHERE email = '$email'";
    $result = $mysqli->query($email_check_sql);

    if ($result->num_rows > 0) {
        echo json_encode('Email already exists. Please use a different email.');
        exit();
    }

    // Prepare the insert statement without spouse_income
    $sql = "INSERT INTO members (member_id, name, contact_no, email, birthday, sex, civil_status, address, place_of_birth, spouse_name, children, education_date, education_school, education_course, employment_date, employment_position, employment_school, cooperative_experience_date, cooperative_experience_position, cooperative_experience_name, training_date, training_course, training_hours, business_present, business_previous, affiliation, member_salary, other_income, crime, role, age)
         VALUES ('$member_id', '$name', '$contact_no', '$email', '$birthday', '$sex', '$civil_status', '$address', '$place_of_birth', '$spouse_name', '$children', '$education_date', '$education_school', '$education_course', '$employment_date', '$employment_position', '$employment_school', '$cooperative_experience_date', '$cooperative_experience_position', '$cooperative_experience_name', '$training_date', '$training_course', '$training_hours', '$business_present', '$business_previous', '$affiliation', '$member_salary', '$other_income', '$crime', 'Member', '$age')";

    if ($mysqli->query($sql) === TRUE) {
        echo json_encode(['New record created successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $mysqli->error]);
    }
}
?>
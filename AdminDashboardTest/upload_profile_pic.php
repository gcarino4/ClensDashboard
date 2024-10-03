<?php
session_start();
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Directory where the file will be uploaded
    $target_dir = "uploads/";
    // Target file path
    $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_pic"]["size"] > 500000) { // 500KB limit
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Check if file already exists and delete it
        if (file_exists($target_file)) {
            if (!unlink($target_file)) {
                echo "Sorry, there was an error deleting the old file.";
                $uploadOk = 0;
            }
        }

        // Attempt to move the uploaded file
        if ($uploadOk == 1 && move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now update the database
            $member_id = $_SESSION['member_id']; // Assuming member_id is stored in session
            $filename = basename($_FILES["profile_pic"]["name"]);

            $sql = "UPDATE members SET profile_pic = ? WHERE member_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $filename, $member_id);

            if ($stmt->execute()) {
                echo "<script>alert('You have successfully updated your profile picture!'); window.location.href='profile_page.php';</script>";
            } else {
                echo "Sorry, there was an error updating the database.";
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $conn->close();
}
?>
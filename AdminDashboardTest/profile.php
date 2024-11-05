<?php
// Fetch user data from the members table
require 'connection.php';

$member_id = $_SESSION['member_id']; // Assuming member_id is stored in session

$sql = "SELECT profile_pic, name, role FROM members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit;
}

$stmt->close();
$conn->close();

$profilePic = !empty($user['profile_pic']) ? 'uploads/' . htmlspecialchars($user['profile_pic']) : 'images/default-profile.png';
$displayName = htmlspecialchars($user['name']);
$role = htmlspecialchars($user['role']);
?>

<a href="profile_page.php" style="text-decoration: none; color: inherit;">
    <div class="profile">
        <div class="info">
            <p>Hey, <b><?php echo $displayName; ?></b></p>
            <small class="text-muted"><?php echo $role; ?></small>
        </div>
        <div class="profile-photo">
            <img src="<?php echo $profilePic; ?>" alt="Profile Picture"
                style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
        </div>

    </div>
    <a href="../logout.php" style="color: red">
        <span class="material-icons-sharp">
            logout
        </span>
    </a>
</a>
<?php
require "connection.php";

// Query to fetch the top 5 new members ordered by date_of_creation
$sql = "SELECT member_id, name, profile_pic, TIMESTAMPDIFF(MINUTE, date_of_creation, NOW()) AS minutes_ago 
        FROM members 
        ORDER BY date_of_creation DESC 
        LIMIT 5";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="new-users">';
    echo '<h2>New Users</h2>';
    echo '<div class="user-list">';

    // Loop through each member and display
    while ($row = $result->fetch_assoc()) {
        $member_id = $row['member_id'];
        $name = htmlspecialchars($row['name']);
        $profile_pic = htmlspecialchars($row['profile_pic']);
        $minutes_ago = $row['minutes_ago'];

        $default_image = 'user.jpg';
        $profile_image_path = 'uploads/' . $profile_pic;

        // Determine the time ago format
        if ($minutes_ago < 60) {
            $time_ago = $minutes_ago . ' Min Ago';
        } elseif ($minutes_ago < 1440) {
            $hours_ago = floor($minutes_ago / 60);
            $time_ago = $hours_ago . ' Hours Ago';
        } else {
            $days_ago = floor($minutes_ago / 1440);
            $time_ago = $days_ago . ' Days Ago';
        }

        // Check if the profile picture exists, otherwise use the default image
        if (empty($profile_pic) || !file_exists($profile_image_path)) {
            $profile_image_path = 'images/' . $default_image;
        }
        echo '<div class="user">';
        echo '<img src="' . $profile_image_path . '" alt="' . htmlspecialchars($name, ENT_QUOTES) . '">';
        echo '<h2>' . $name . '</h2>';
        echo '<p>' . $time_ago . '</p>';
        echo '</div>';
    }

    echo '</div>'; // Close user-list
    echo '</div>'; // Close new-users
} else {
    echo 'No new users found.';
}

// Close connection
$conn->close();
?>
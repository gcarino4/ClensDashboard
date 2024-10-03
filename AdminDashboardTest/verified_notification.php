<?php
require 'get_member_verified.php';
?>
<div class="reminders">
    <div class="header">
        <h2>Notification</h2>
        <span class="material-icons-sharp">
            notifications_none
        </span>
    </div>

    <?php if ($verified == 'True'): ?>
        <div class="notification">
            <div class="icon">
                <span class="material-icons-sharp">
                    check_circle
                </span>
            </div>
            <div class="content">
                <div class="info">
                    <h3>You are now verified!</h3>
                    <small class="text_muted">
                        Your account has been successfully verified.
                    </small>
                </div>
                <span class="material-icons-sharp">
                    more_vert
                </span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Other notifications here -->
</div>
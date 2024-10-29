<?php
$role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Get the role from session, default to empty
?>

<!-- Sidebar Section -->
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<aside>
    <div class="toggle">
        <div class="logo">
            <img src="images/CoLens.png">
            <h2>CoLens<span class="success">Tech</span></h2>
        </div>
        <div class="close" id="close-btn">
            <span class="material-icons-sharp">
                close
            </span>
        </div>
    </div>

    <div class="sidebar">

        <?php if ($role === 'Admin' || $role === 'Admin Officer' || $role === 'Finance Officer'): ?>
            <a href="index.php" id="dashboard-link">
                <span class="material-icons-sharp">
                    dashboard
                </span>
                <h3>Dashboard</h3>
            </a>
        <?php endif; ?>


        <?php if ($role === 'Member'): ?>
            <a href="index_member.php" id="main-menu-link">
                <span class="material-symbols-outlined">
                    widgets
                </span>
                <h3>Services</h3>
            </a>

            <a href="general_ledger.php">
                <span class="material-icons-sharp">
                    menu_book
                </span>
                <h3>Journal</h3>
            </a>

            <a href="member_payment.php" id="settings-link">
                <span class="material-icons-sharp">
                    attach_money
                </span>
                <h3>Payments</h3>
            </a>

        <?php endif; ?>


        <?php if ($role === 'Admin' || $role === 'Finance Officer'): ?>

            <a href="general_ledger.php">
                <span class="material-icons-sharp">
                    menu_book
                </span>
                <h3>General Ledger</h3>
            </a>



            <a href="contribution_admin.php">
                <span class="material-icons-sharp">
                    payment
                </span>
                <h3>Add Contribution</h3>
            </a>

            <a href="analytics.php" id="analytics-link">
                <span class="material-icons-sharp">
                    insights
                </span>
                <h3>Analytics</h3>
            </a>

            <a href="cashflow.php">
                <span class="material-icons-sharp">
                    import_export
                </span>
                <h3>Cashflow</h3>
                <span class="message-count">80</span>
            </a>

            <a href="income_statement.php">
                <span class="material-icons-sharp">
                    receipt
                </span>
                <h3>Income Statement</h3>
                <span class="message-count">80</span>
            </a>

            <a href="balance_sheet.php">
                <span class="material-icons-sharp">
                    payments
                </span>
                <h3>Balance Sheet</h3>
                <span class="message-count">80</span>
            </a>

            <a href="accounts-payable.php">
                <span class="material-icons-sharp">
                    payments
                </span>
                <h3>Accounts Payable</h3>
                <span class="message-count">80</span>
            </a>

            <a href="accounts-equity.php">
                <span class="material-icons-sharp">
                    equalizer
                </span>
                <h3>Accounts Equity</h3>
            </a>

            <a href="accounts-revenue.php">
                <span class="material-icons-sharp">
                    equalizer
                </span>
                <h3>Revenue</h3>
            </a>

            <a href="accounts-receivable.php">
                <span class="material-icons-sharp">
                    receipt_long
                </span>
                <h3>Accounts Receivable</h3>
                <span class="message-count">25</span>
            </a>

        <?php endif; ?>


        <?php if ($role === 'Admin' || $role === 'Admin Officer'): ?>
            <a href="./user-account-management.php" id="user-account-management-link">
                <span class="material-icons-sharp">
                    person_add
                </span>
                <h3>User Account Management</h3>
            </a>

            <a href="admin.php" id="admin-link">
                <span class="material-icons-sharp">
                    person_outline
                </span>
                <h3>Admin</h3>
            </a>
        <?php endif; ?>


        <?php if ($role === 'Member' || $role === 'Admin' || $role === 'Admin Officer' || $role === 'Finance Officer'): ?>
            <a href="profile_page.php" id="profile-link">
                <span class="material-icons-sharp">
                    person
                </span>
                <h3>Profile</h3>
            </a>
        <?php endif; ?>


        <a href="../logout.php">
            <span class="material-icons-sharp">
                logout
            </span>
            <h3>Logout</h3>
        </a>

    </div>
</aside>
<!-- End of Sidebar Section -->
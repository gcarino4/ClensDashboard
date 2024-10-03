const sideMenu = document.querySelector('aside');
const menuBtn = document.getElementById('menu-btn');
const closeBtn = document.getElementById('close-btn');

const darkMode = document.querySelector('.dark-mode');

menuBtn.addEventListener('click', () => {
    sideMenu.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
    sideMenu.style.display = 'none';
});

darkMode.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode-variables');
    darkMode.querySelector('span:nth-child(1)').classList.toggle('active');
    darkMode.querySelector('span:nth-child(2)').classList.toggle('active');
})



// index.js

document.addEventListener("DOMContentLoaded", () => {
    const links = [
        { id: 'dashboard-link', url: 'index.php' },
        { id: 'main-menu-link', url: 'index_member.php' },
        { id: 'admin-link', url: 'admin.php' },
        { id: 'general-ledger-link', url: 'general_ledger.php' },
        { id: 'analytics-link', url: 'analytics.php' },
        { id: 'accounts-payable-link', url: 'accounts-payable.php' },
        { id: 'accounts-receivable-link', url: 'accounts-receivable.php' },
        { id: 'assets-link', url: 'assets.php' },
        { id: 'user-account-management-link', url: 'user-account-management.php' },
        { id: 'settings-link', url: 'settings.php' },
        { id: 'logout-link', url: 'logout.php' }
    ];

    links.forEach(link => {
        const element = document.getElementById(link.id);
        if (element) {
            element.addEventListener('click', (event) => {
                event.preventDefault(); // Prevent the default anchor behavior
                window.location.href = link.url; // Redirect to the new page
            });
        } else {
            console.warn(`Element with ID '${link.id}' not found.`);
        }
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-btn');
    const aside = document.querySelector('aside');

    menuBtn.addEventListener('click', () => {
        aside.classList.add('open');
    });

    closeBtn.addEventListener('click', () => {
        aside.classList.remove('open');
    });

    const darkModeToggle = document.querySelector('.dark-mode');
    const lightModeIcon = darkModeToggle.querySelector('.material-icons-sharp:nth-child(1)');
    const darkModeIcon = darkModeToggle.querySelector('.material-icons-sharp:nth-child(2)');

    darkModeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        lightModeIcon.classList.toggle('active');
        darkModeIcon.classList.toggle('active');
    });

    const reminders = document.querySelector('.reminders');
    const addReminder = reminders.querySelector('.add-reminder');

    addReminder.addEventListener('click', () => {
        const newReminder = document.createElement('div');
        newReminder.classList.add('notification');
        newReminder.innerHTML = `
            <div class="icon">
                <span class="material-icons-sharp">event</span>
            </div>
            <div class="content">
                <div class="info">
                    <h3>New Reminder</h3>
                    <small class="text_muted">Time</small>
                </div>
                <span class="material-icons-sharp">more_vert</span>
            </div>
        `;
        reminders.insertBefore(newReminder, addReminder);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-btn');
    const sidebar = document.querySelector('aside');
    const darkModeToggle = document.querySelector('.dark-mode');

    menuBtn.addEventListener('click', () => {
        sidebar.style.display = 'block';
    });

    closeBtn.addEventListener('click', () => {
        sidebar.style.display = 'none';
    });

    darkModeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const icons = darkModeToggle.querySelectorAll('span');
        icons.forEach(icon => icon.classList.toggle('active'));
    });
});




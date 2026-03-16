document.addEventListener('DOMContentLoaded', () => {
    // 1. Handle Sidebar Navigation Clicks
    const sidebarItems = document.querySelectorAll('.sidebar li');

    sidebarItems.forEach(item => {
        item.addEventListener('click', () => {
            const pageName = item.getAttribute('data-page');
            if (pageName) {
                window.location.href = `${pageName}.html`;
            }
        });
    });

    // 2. Handle Logout Button
    const logoutBtn = document.querySelector('.user-info a');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm("Are you sure you want to logout of KIMS?")) {
                window.location.href = 'login.html';
            }
        });
    }
});
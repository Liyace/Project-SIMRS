/**
 * RS Delta SIMRS - UI Interactions
 * Mengelola Collapse/Expand Sidebar & Mode Gelap / Terang (Dark/Light Mode)
 */

(function () {
    'use strict';

    // 1. Terapkan state secepat mungkin sebelum render selesai (hindari flash)
    function applySavedPreferences() {
        const savedTheme = localStorage.getItem('simrs_theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
            if (document.body) document.body.classList.add('dark-mode');
        }

        const isCollapsed = localStorage.getItem('simrs_sidebar_collapsed') === 'true';
        if (isCollapsed && document.body) {
            document.body.classList.add('sidebar-collapsed');
            const sb = document.querySelector('.sidebar');
            if (sb) sb.classList.add('collapsed');
        }
    }

    // Eksekusi awal
    applySavedPreferences();

    document.addEventListener('DOMContentLoaded', function () {
        // Sinkronisasi body class jika belum terpasang
        const savedTheme = localStorage.getItem('simrs_theme') || 'light';
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }

        const isCollapsed = localStorage.getItem('simrs_sidebar_collapsed') === 'true';
        const sidebar = document.querySelector('.sidebar');
        if (isCollapsed) {
            document.body.classList.add('sidebar-collapsed');
            if (sidebar) sidebar.classList.add('collapsed');
        }

        // Update tampilan tombol tema
        updateThemeToggleUI(savedTheme);
        updateSidebarToggleUI(isCollapsed);

        // Bind event toggle sidebar
        const btnToggleSidebar = document.getElementById('btn-toggle-sidebar');
        if (btnToggleSidebar) {
            btnToggleSidebar.addEventListener('click', toggleSidebar);
        }

        // Bind event toggle tema
        const btnToggleTheme = document.getElementById('btn-toggle-theme');
        if (btnToggleTheme) {
            btnToggleTheme.addEventListener('click', toggleTheme);
        }

        // Mencegah efek drag / seleksi teks putih pada topbar
        const topbarEl = document.querySelector('.topbar');
        if (topbarEl) {
            topbarEl.addEventListener('dragstart', function (e) { e.preventDefault(); });
            topbarEl.addEventListener('selectstart', function (e) { e.preventDefault(); });
        }

        // Live Clock Jam & Tanggal RS Delta
        initLiveClock();
    });

    function initLiveClock() {
        const clockEl = document.getElementById('clock-date-time');
        if (!clockEl) return;

        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            clockEl.textContent = `${dayName}, ${date} ${monthName} ${year} • ${hours}:${minutes}:${seconds} WITA`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    }

    // Toggle Collapse / Expand Sidebar
    function toggleSidebar() {
        const body = document.body;
        const sidebar = document.querySelector('.sidebar');
        const willCollapse = !body.classList.contains('sidebar-collapsed');

        if (willCollapse) {
            body.classList.add('sidebar-collapsed');
            if (sidebar) sidebar.classList.add('collapsed');
            localStorage.setItem('simrs_sidebar_collapsed', 'true');
        } else {
            body.classList.remove('sidebar-collapsed');
            if (sidebar) sidebar.classList.remove('collapsed');
            localStorage.setItem('simrs_sidebar_collapsed', 'false');
        }

        updateSidebarToggleUI(willCollapse);
    }

    function updateSidebarToggleUI(isCollapsed) {
        const toggleIcon = document.getElementById('sidebar-toggle-icon');
        if (toggleIcon) {
            if (isCollapsed) {
                toggleIcon.className = 'fa-solid fa-chevron-right';
            } else {
                toggleIcon.className = 'fa-solid fa-chevron-left';
            }
        }
    }

    // Toggle Dark / Light Theme
    function toggleTheme() {
        const isDark = document.body.classList.contains('dark-mode');
        const nextTheme = isDark ? 'light' : 'dark';

        if (nextTheme === 'dark') {
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark-mode');
            localStorage.setItem('simrs_theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark-mode');
            localStorage.setItem('simrs_theme', 'light');
        }

        updateThemeToggleUI(nextTheme);
    }

    function updateThemeToggleUI(theme) {
        const icon = document.getElementById('theme-toggle-icon');
        const text = document.getElementById('theme-toggle-text');

        if (icon) {
            if (theme === 'dark') {
                icon.className = 'fa-solid fa-sun';
            } else {
                icon.className = 'fa-solid fa-moon';
            }
        }

        if (text) {
            text.textContent = (theme === 'dark') ? 'Mode Terang' : 'Mode Gelap';
        }
    }

    // Expose fungsi ke window jika dibutuhkan inline
    window.simrsToggleSidebar = toggleSidebar;
    window.simrsToggleTheme = toggleTheme;
})();

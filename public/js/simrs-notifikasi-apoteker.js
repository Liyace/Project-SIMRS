/**
 * RS Delta SIMRS - Notifikasi Antrean E-Resep Apoteker Realtime
 * Mengelola deteksi resep baru yang siap diracik / diserahkan dari kasir & dokter,
 * badge counter topbar/sidebar, dropdown popover, dan notifikasi visual.
 */

(function () {
    'use strict';

    const NOTIF_ENDPOINT = '/apoteker/notifikasi';
    const POLL_INTERVAL = 15000; // 15 detik

    let lastKnownCount = null;
    let isFirstRun = true;

    document.addEventListener('DOMContentLoaded', function () {
        initNotifikasiApoteker();
    });

    function initNotifikasiApoteker() {
        const btnNotif = document.getElementById('btn-topbar-notif-apoteker');
        const dropdown = document.getElementById('dropdown-notif-apoteker');

        if (!btnNotif || !dropdown) return;

        // Toggle buka/tutup dropdown
        btnNotif.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                fetchNotifikasiApoteker();
            }
        });

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !btnNotif.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // Jalankan polling awal & berkala
        fetchNotifikasiApoteker();
        setInterval(fetchNotifikasiApoteker, POLL_INTERVAL);
    }

    async function fetchNotifikasiApoteker() {
        try {
            const res = await fetch(NOTIF_ENDPOINT, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) return;

            const data = await res.json();
            renderNotifikasiApoteker(data);
        } catch (err) {
            console.error('Error polling notifikasi apoteker:', err);
        }
    }

    function renderNotifikasiApoteker(data) {
        const totalCount = data.count_resep || 0;
        const badgeTopbar = document.getElementById('topbar-notif-count-apoteker');
        const pulseTopbar = document.getElementById('topbar-notif-pulse-apoteker');
        const badgeSidebar = document.getElementById('sidebar-notif-count-apoteker');
        const headerCount = document.getElementById('dropdown-header-count-apoteker');
        const listContainer = document.getElementById('dropdown-notif-list-apoteker');

        // Update badge topbar
        if (badgeTopbar) {
            if (totalCount > 0) {
                badgeTopbar.textContent = totalCount > 99 ? '99+' : totalCount;
                badgeTopbar.style.display = 'inline-flex';
                if (pulseTopbar) pulseTopbar.style.display = 'block';
            } else {
                badgeTopbar.style.display = 'none';
                if (pulseTopbar) pulseTopbar.style.display = 'none';
            }
        }

        // Update badge sidebar
        if (badgeSidebar) {
            if (totalCount > 0) {
                badgeSidebar.textContent = totalCount > 99 ? '99+' : totalCount;
                badgeSidebar.style.display = 'inline-flex';
            } else {
                badgeSidebar.style.display = 'none';
            }
        }

        // Update header counter di dropdown
        if (headerCount) {
            headerCount.textContent = `${totalCount} Resep`;
        }

        // Render isi daftar resep
        if (listContainer) {
            const resepList = data.resep_antrean || [];

            if (resepList.length === 0) {
                listContainer.innerHTML = `
                    <div class="notif-empty-state">
                        <i class="fa-solid fa-clipboard-check" style="color: #10b981; font-size: 28px; margin-bottom: 8px;"></i>
                        <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 13px;">Semua Resep Telah Diserahkan</p>
                        <span style="font-size: 11px; color: #64748b;">Tidak ada antrean resep yang menunggu saat ini.</span>
                    </div>
                `;
            } else {
                let html = '<ul class="notif-list-items" style="list-style: none; padding: 0; margin: 0;">';
                resepList.forEach(r => {
                    const statusText = r.status_resep === 'siap_diambil' ? 'Siap Diambil' : 'Siap Diracing';
                    const statusColor = r.status_resep === 'siap_diambil' ? '#15803d' : '#2563eb';
                    const statusBg = r.status_resep === 'siap_diambil' ? '#f0fdf4' : '#eff6ff';

                    html += `
                        <li class="notif-item" style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <strong style="font-size: 12.5px; color: #1e293b;">${r.nama_pasien}</strong>
                                    <span style="font-size: 11px; color: #64748b;">(${r.rm})</span>
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    <span>${r.no_resep}</span> • <span>${r.poli}</span> • <span>${r.waktu} WITA</span>
                                </div>
                            </div>
                            <div>
                                <span style="font-size: 10.5px; font-weight: 700; background: ${statusBg}; color: ${statusColor}; padding: 3px 8px; border-radius: 6px;">
                                    ${statusText}
                                </span>
                            </div>
                        </li>
                    `;
                });
                html += '</ul>';
                listContainer.innerHTML = html;
            }
        }

        lastKnownCount = totalCount;
        isFirstRun = false;
    }
})();

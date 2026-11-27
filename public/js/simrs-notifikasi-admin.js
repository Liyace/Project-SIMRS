/**
 * RS Delta SIMRS - Notifikasi Permintaan Reset Real-time untuk Role Administrator
 * Menangani lonceng topbar, dropdown daftar permohonan pending, dan badge sidebar.
 */
(function () {
    'use strict';

    const NOTIF_ENDPOINT = '/admin/permintaan-reset/notifikasi';
    const POLL_INTERVAL = 12000; // 12 detik

    let lastKnownCount = null;

    document.addEventListener('DOMContentLoaded', function () {
        initNotifikasiAdmin();
    });

    function initNotifikasiAdmin() {
        const btnNotif = document.getElementById('btn-topbar-notif-admin');
        const dropdown = document.getElementById('dropdown-notif-admin');

        if (btnNotif && dropdown) {
            // Toggle buka/tutup dropdown
            btnNotif.addEventListener('click', function (e) {
                e.stopPropagation();
                const isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';
                if (!isVisible) {
                    fetchNotifikasiAdmin();
                }
            });

            // Tutup dropdown saat klik di luar
            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && !btnNotif.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Cegah penutupan saat klik di dalam dropdown
            dropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // Jalankan polling awal & berkala
        fetchNotifikasiAdmin();
        setInterval(fetchNotifikasiAdmin, POLL_INTERVAL);
    }

    async function fetchNotifikasiAdmin() {
        try {
            const res = await fetch(NOTIF_ENDPOINT, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) return;

            const data = await res.json();
            updateNotifikasiAdminUI(data.count || 0, data.list || []);

            // Jika ada permintaan baru dan admin sedang berada di halaman reset
            if (lastKnownCount !== null && data.count > lastKnownCount && data.terbaru) {
                if (window.location.pathname.includes('/admin/permintaan-reset')) {
                    const notifyConfirm = confirm(`[Notifikasi SIMRS Delta]\nAda permohonan reset baru dari ${data.terbaru.nama} (${data.terbaru.tipe_permintaan}).\n\nMuat ulang tabel permohonan sekarang?`);
                    if (notifyConfirm) {
                        window.location.reload();
                    }
                }
            }

            lastKnownCount = data.count;
        } catch (err) {
            // diamkan error jaringan sesaat
        }
    }

    function updateNotifikasiAdminUI(count, items) {
        // 1. Update Badge Topbar
        const badgeCount = document.getElementById('topbar-notif-count-admin');
        const pulseDot   = document.getElementById('topbar-notif-pulse-admin');
        const headerCount= document.getElementById('dropdown-header-count-admin');
        const listBody   = document.getElementById('dropdown-notif-list-admin');
        const sidebarPill= document.getElementById('badge-admin-reset');

        if (count > 0) {
            if (badgeCount) {
                badgeCount.textContent = count > 99 ? '99+' : count;
                badgeCount.style.display = 'inline-flex';
            }
            if (pulseDot) {
                pulseDot.style.display = 'block';
            }
            if (sidebarPill) {
                sidebarPill.textContent = count > 99 ? '99+' : count;
                sidebarPill.style.display = '';
            }
            if (headerCount) {
                headerCount.textContent = `${count} Permintaan Menunggu`;
            }
        } else {
            if (badgeCount) badgeCount.style.display = 'none';
            if (pulseDot) pulseDot.style.display = 'none';
            if (sidebarPill) sidebarPill.style.display = 'none';
            if (headerCount) headerCount.textContent = '0 Permintaan';
        }

        // 2. Render List di Popover Dropdown
        if (listBody) {
            if (count === 0) {
                listBody.innerHTML = `
                    <div class="notif-empty-state" style="text-align: center; padding: 22px 14px; color: #94a3b8;">
                        <i class="fa-solid fa-circle-check" style="font-size: 26px; color: #10b981; margin-bottom: 6px; display: block;"></i>
                        <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 13px;">Semua Permintaan Bersih</p>
                        <p style="margin: 4px 0 0; font-size: 11.5px; color: #64748b;">Tidak ada permohonan reset yang menunggu saat ini.</p>
                    </div>
                `;
            } else {
                let html = '';
                items.forEach(req => {
                    html += `
                        <div class="notif-item" style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <strong style="font-size: 12.5px; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(req.nama)}</strong>
                                    <span style="font-size: 10px; font-weight: 700; background: #fee2e2; color: #b91c1c; padding: 1px 6px; border-radius: 4px;">${escapeHtml(req.tipe_permintaan)}</span>
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    <span>${escapeHtml(req.tipe_pengguna)}</span> • <span>NIK: ${escapeHtml(req.nik)}</span> • <span style="color: #94a3b8;">${escapeHtml(req.waktu)}</span>
                                </div>
                            </div>
                            <a href="/admin/permintaan-reset" style="background: #1C4D8D; color: white; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0;">
                                Buka <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    `;
                });

                if (count > items.length) {
                    html += `
                        <div style="text-align: center; padding: 8px; font-size: 11.5px; color: #64748b; font-style: italic;">
                            + ${count - items.length} permohonan lainnya
                        </div>
                    `;
                }

                listBody.innerHTML = html;
            }
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
})();

/**
 * RS Delta SIMRS - Notifikasi Antrean Skrining Perawat Realtime
 * Mengelola deteksi antrean pasien yang menunggu skrining TTV,
 * badge counter topbar/sidebar, dropdown antrean, dan notifikasi visual.
 */

(function () {
    'use strict';

    const NOTIF_ENDPOINT = '/perawat/notifikasi';
    const POLL_INTERVAL = 15000; // 15 detik

    let lastKnownCount = null;
    let knownPatientIds = new Set();
    let isFirstRun = true;

    document.addEventListener('DOMContentLoaded', function () {
        initNotifikasiPerawat();
    });

    function initNotifikasiPerawat() {
        const btnNotif = document.getElementById('btn-topbar-notif-perawat');
        const dropdown = document.getElementById('dropdown-notif-perawat');

        if (!btnNotif || !dropdown) return;

        // Toggle buka/tutup dropdown
        btnNotif.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                fetchNotifikasiPerawat();
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
        fetchNotifikasiPerawat();
        setInterval(fetchNotifikasiPerawat, POLL_INTERVAL);
    }

    async function fetchNotifikasiPerawat() {
        try {
            const res = await fetch(NOTIF_ENDPOINT, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) return;

            const data = await res.json();
            if (data.success) {
                updateNotifikasiPerawatUI(data.count || 0, data.pasien || []);
            }
        } catch (err) {
            console.warn('[SIMRS] Gagal memeriksa notifikasi antrean perawat:', err);
        }
    }

    function updateNotifikasiPerawatUI(count, pasienList) {
        const badgeCount = document.getElementById('topbar-notif-count-perawat');
        const pulseDot   = document.getElementById('topbar-notif-pulse-perawat');
        const headerCount= document.getElementById('dropdown-header-count-perawat');
        const listBody   = document.getElementById('dropdown-notif-list-perawat');
        const sidebarPill= document.getElementById('sidebar-notif-count-perawat');

        if (count > 0) {
            if (badgeCount) {
                badgeCount.textContent = count > 99 ? '99+' : count;
                badgeCount.style.display = 'inline-flex';
            }
            if (pulseDot) {
                pulseDot.style.display = 'block';
            }
            if (sidebarPill) {
                sidebarPill.textContent = count;
                sidebarPill.style.display = 'inline-block';
            }
            if (headerCount) {
                headerCount.textContent = `${count} Pasien Menunggu`;
            }
        } else {
            if (badgeCount) badgeCount.style.display = 'none';
            if (pulseDot) pulseDot.style.display = 'none';
            if (sidebarPill) sidebarPill.style.display = 'none';
            if (headerCount) headerCount.textContent = '0 Pasien';
        }

        // Render List Pasien di Dropdown (Minimalis, Ringkas, dan Tidak Menumpuk)
        if (listBody) {
            if (!pasienList || pasienList.length === 0) {
                listBody.innerHTML = `
                    <div class="notif-empty-state">
                        <i class="fa-solid fa-circle-check" style="font-size: 28px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                        Semua pasien poliklinik telah selesai diskrining.
                    </div>
                `;
            } else {
                let html = '';
                // Tampilkan maksimal 5 pasien terbaru agar popover tetap bersih dan ringkas
                const tampilPasien = pasienList.slice(0, 5);
                tampilPasien.forEach(p => {
                    html += `
                        <a href="${escapeHtml(p.url_skrining)}" class="notif-item-simple" title="Klik untuk langsung skrining ${escapeHtml(p.nama_pasien)}">
                            <div class="notif-icon-circle">
                                <i class="fa-solid fa-heart-pulse"></i>
                            </div>
                            <div class="notif-simple-content">
                                <div class="notif-simple-title">
                                    <strong>${escapeHtml(p.nama_pasien)}</strong>
                                    <span class="notif-badge-code">${escapeHtml(p.no_antrian)}</span>
                                </div>
                                <div class="notif-simple-sub">
                                    <span>${escapeHtml(p.nama_poli)}</span>
                                    <span>•</span>
                                    <span>${escapeHtml(p.waktu_daftar)} WITA</span>
                                </div>
                            </div>
                            <div class="notif-arrow-icon">
                                <i class="fa-solid fa-chevron-right"></i>
                            </div>
                        </a>
                    `;
                });

                if (pasienList.length > 5) {
                    html += `
                        <div style="text-align: center; padding: 6px; font-size: 11.5px; color: #64748b; font-style: italic;">
                            + ${pasienList.length - 5} pasien lainnya di antrean
                        </div>
                    `;
                }

                listBody.innerHTML = html;
            }
        }

        // Toast notifikasi saat ada pasien baru tiba
        if (!isFirstRun && lastKnownCount !== null && count > lastKnownCount) {
            const newPatients = pasienList.filter(p => !knownPatientIds.has(p.id_pendaftaran));
            if (newPatients.length > 0) {
                const latest = newPatients[0];
                tampilkanToastPerawat(
                    `Pasien Baru Menunggu Skrining`,
                    `${latest.nama_pasien} (${latest.no_antrian}) telah tiba di ${latest.nama_poli}.`
                );
            }
        }

        knownPatientIds = new Set(pasienList.map(p => p.id_pendaftaran));
        lastKnownCount = count;
        isFirstRun = false;
    }

    function tampilkanToastPerawat(judul, pesan) {
        const toast = document.createElement('div');
        toast.className = 'simrs-toast';
        toast.innerHTML = `
            <div class="simrs-toast-icon" style="background: #2563eb; color: white;">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div class="simrs-toast-content">
                <div class="simrs-toast-title">${escapeHtml(judul)}</div>
                <div class="simrs-toast-msg">${escapeHtml(pesan)}</div>
            </div>
            <button type="button" class="simrs-toast-close">&times;</button>
        `;

        toast.querySelector('.simrs-toast-close').addEventListener('click', () => {
            toast.remove();
        });

        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 50);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 6000);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
})();

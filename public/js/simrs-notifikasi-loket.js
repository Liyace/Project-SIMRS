/**
 * RS Delta SIMRS - Notifikasi Pendaftaran Loket Realtime
 * Mengelola deteksi pendaftaran pasien baru (walk-in & online),
 * counter pendaftaran hari ini, dan dropdown list.
 */

(function () {
    'use strict';

    const NOTIF_ENDPOINT = '/loket/notifikasi';
    const POLL_INTERVAL = 12000; // 12 detik

    let lastKnownTotal = null;
    let isFirstRun = true;

    document.addEventListener('DOMContentLoaded', function () {
        initNotifikasiLoket();
    });

    function initNotifikasiLoket() {
        const btnNotif = document.getElementById('btn-topbar-notif-loket');
        const dropdown = document.getElementById('dropdown-notif-loket');

        if (!btnNotif || !dropdown) return;

        btnNotif.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                fetchNotifikasiLoket();
            }
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !btnNotif.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        fetchNotifikasiLoket();
        setInterval(fetchNotifikasiLoket, POLL_INTERVAL);
    }

    async function fetchNotifikasiLoket() {
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
                updateNotifikasiLoketUI(data);
            }
        } catch (err) {
            console.warn('[SIMRS] Gagal memeriksa notifikasi loket:', err);
        }
    }

    function updateNotifikasiLoketUI(data) {
        const badgeCount = document.getElementById('topbar-notif-count-loket');
        const pulseDot   = document.getElementById('topbar-notif-pulse-loket');
        const headerCount= document.getElementById('dropdown-header-count-loket');
        const listBody   = document.getElementById('dropdown-notif-list-loket');

        const total = data.total_hari_ini || 0;
        const menunggu = data.menunggu_skrining || 0;
        const pendaftaranList = data.pendaftaran || [];

        if (total > 0) {
            if (badgeCount) {
                badgeCount.textContent = total > 99 ? '99+' : total;
                badgeCount.style.display = 'inline-flex';
            }
            if (pulseDot && menunggu > 0) {
                pulseDot.style.display = 'block';
            }
            if (headerCount) {
                headerCount.textContent = `${total} Pasien (${menunggu} Menunggu)`;
            }
        } else {
            if (badgeCount) badgeCount.style.display = 'none';
            if (pulseDot) pulseDot.style.display = 'none';
            if (headerCount) headerCount.textContent = '0 Pasien';
        }

        if (listBody) {
            if (!pendaftaranList || pendaftaranList.length === 0) {
                listBody.innerHTML = `
                    <div class="notif-empty-state">
                        <i class="fa-solid fa-clipboard-user" style="font-size: 28px; color: #94a3b8; margin-bottom: 8px; display: block;"></i>
                        Belum ada pendaftaran pasien hari ini.
                    </div>
                `;
            } else {
                let html = '';
                pendaftaranList.forEach(p => {
                    html += `
                        <a href="${escapeHtml(p.url_karcis || '/loket/pendaftaran')}" target="_blank" class="notif-item-simple" title="Lihat karcis ${escapeHtml(p.nama_pasien)}">
                            <div class="notif-icon-circle" style="background: #eef2ff; color: #4338ca;">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div class="notif-simple-content">
                                <div class="notif-simple-title">
                                    <strong>${escapeHtml(p.nama_pasien)}</strong>
                                    <span class="notif-badge-code" style="background: #e0e7ff; color: #3730a3;">${escapeHtml(p.no_antrian)}</span>
                                </div>
                                <div class="notif-simple-sub">
                                    <span>${escapeHtml(p.nama_poli)}</span>
                                    <span>•</span>
                                    <span style="font-weight: 600;">${escapeHtml(p.cara_bayar)}</span>
                                    <span>•</span>
                                    <span>${escapeHtml(p.waktu)} WITA</span>
                                </div>
                            </div>
                            <div class="notif-arrow-icon">
                                <i class="fa-solid fa-print"></i>
                            </div>
                        </a>
                    `;
                });

                listBody.innerHTML = html;
            }
        }

        // Toast jika ada pendaftaran baru bertambah
        if (!isFirstRun && lastKnownTotal !== null && total > lastKnownTotal) {
            if (pendaftaranList.length > 0) {
                const latest = pendaftaranList[0];
                tampilkanToastLoket(
                    `Pendaftaran Pasien Baru`,
                    `${latest.nama_pasien} (${latest.no_antrian}) terdaftar di ${latest.nama_poli}.`
                );
            }
        }

        lastKnownTotal = total;
        isFirstRun = false;
    }

    function tampilkanToastLoket(judul, pesan) {
        const toast = document.createElement('div');
        toast.className = 'simrs-toast';
        toast.innerHTML = `
            <div class="simrs-toast-icon" style="background: #4f46e5; color: white;">
                <i class="fa-solid fa-id-card"></i>
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

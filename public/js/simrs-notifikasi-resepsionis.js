/**
 * RS Delta SIMRS - Notifikasi Pengawasan Antrean Resepsionis Realtime
 * Mengawasi lonjakan antrean (bottlenecks), panggilan aktif,
 * dan peringatan penumpukan pos pelayanan.
 */

(function () {
    'use strict';

    const NOTIF_ENDPOINT = '/resepsionis/notifikasi';
    const POLL_INTERVAL = 10000; // 10 detik

    let lastBottleneckCount = 0;
    let isFirstRun = true;

    document.addEventListener('DOMContentLoaded', function () {
        initNotifikasiResepsionis();
    });

    function initNotifikasiResepsionis() {
        const btnNotif = document.getElementById('btn-topbar-notif-resepsionis');
        const dropdown = document.getElementById('dropdown-notif-resepsionis');

        if (!btnNotif || !dropdown) return;

        btnNotif.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                fetchNotifikasiResepsionis();
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

        fetchNotifikasiResepsionis();
        setInterval(fetchNotifikasiResepsionis, POLL_INTERVAL);
    }

    async function fetchNotifikasiResepsionis() {
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
                updateNotifikasiResepsionisUI(data);
            }
        } catch (err) {
            console.warn('[SIMRS] Gagal memeriksa notifikasi resepsionis:', err);
        }
    }

    function updateNotifikasiResepsionisUI(data) {
        const badgeCount = document.getElementById('topbar-notif-count-resepsionis');
        const pulseDot   = document.getElementById('topbar-notif-pulse-resepsionis');
        const headerCount= document.getElementById('dropdown-header-count-resepsionis');
        const listBody   = document.getElementById('dropdown-notif-list-resepsionis');

        const totalPeringatan = data.total_peringatan || 0;
        const bottlenecks = data.bottlenecks || [];

        if (totalPeringatan > 0) {
            if (badgeCount) {
                badgeCount.textContent = totalPeringatan;
                badgeCount.style.display = 'inline-flex';
                badgeCount.style.background = '#ef4444';
            }
            if (pulseDot) {
                pulseDot.style.display = 'block';
                pulseDot.style.background = '#ef4444';
            }
            if (headerCount) {
                headerCount.textContent = `${totalPeringatan} Peringatan Antrean`;
                headerCount.style.background = '#fee2e2';
                headerCount.style.color = '#b91c1c';
            }
        } else {
            if (badgeCount) badgeCount.style.display = 'none';
            if (pulseDot) pulseDot.style.display = 'none';
            if (headerCount) {
                headerCount.textContent = 'Alur Antrean Lancar';
                headerCount.style.background = '#dcfce7';
                headerCount.style.color = '#15803d';
            }
        }

        if (listBody) {
            if (bottlenecks.length === 0) {
                listBody.innerHTML = `
                    <div class="notif-empty-state">
                        <i class="fa-solid fa-circle-check" style="font-size: 28px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                        Seluruh pos antrean berjalan normal & kondusif.
                        <div style="font-size: 11px; color: #64748b; margin-top: 6px;">
                            Skrining: ${data.total_skrining} • Dokter: ${data.total_dokter} • Kasir: ${data.total_kasir} • Farmasi: ${data.total_farmasi}
                        </div>
                    </div>
                `;
            } else {
                let html = '';
                bottlenecks.forEach(b => {
                    html += `
                        <div class="notif-item-simple" style="background: #fff5f5; border-color: #fecaca;">
                            <div class="notif-icon-circle" style="background: #fee2e2; color: #dc2626;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="notif-simple-content">
                                <div class="notif-simple-title" style="color: #991b1b;">
                                    <strong>${escapeHtml(b.pos)}</strong>
                                    <span class="notif-badge-code" style="background: #ef4444; color: white;">${escapeHtml(b.count)} Pasien</span>
                                </div>
                                <div class="notif-simple-sub" style="color: #7f1d1d;">
                                    ${escapeHtml(b.pesan)}
                                </div>
                            </div>
                        </div>
                    `;
                });

                listBody.innerHTML = html;
            }
        }

        // Toast bila ada peringatan penumpukan baru
        if (!isFirstRun && totalPeringatan > lastBottleneckCount) {
            const latest = bottlenecks[0];
            tampilkanToastResepsionis(
                `Peringatan Penumpukan Antrean`,
                latest.pesan
            );
        }

        lastBottleneckCount = totalPeringatan;
        isFirstRun = false;
    }

    function tampilkanToastResepsionis(judul, pesan) {
        const toast = document.createElement('div');
        toast.className = 'simrs-toast';
        toast.innerHTML = `
            <div class="simrs-toast-icon" style="background: #dc2626; color: white;">
                <i class="fa-solid fa-triangle-exclamation"></i>
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
        }, 7000);
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

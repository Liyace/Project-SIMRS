/**
 * RS Delta SIMRS - Notifikasi Antrean Kasir Realtime
 * Mengelola deteksi antrean pasien siap bayar, badge counter, popover list,
 * dan toast audio chime secara otomatis.
 */

(function () {
    'use strict';

    const NOTIF_ENDPOINT = '/antrian/notifikasi';
    const POLL_INTERVAL = 15000; // 15 detik

    let lastKnownCount = null;
    let knownPatientIds = new Set();
    let isFirstRun = true;

    document.addEventListener('DOMContentLoaded', function () {
        initNotifikasiKasir();
    });

    function initNotifikasiKasir() {
        const btnNotif = document.getElementById('btn-topbar-notif');
        const dropdown = document.getElementById('dropdown-notif-kasir');

        if (!btnNotif || !dropdown) return;

        // Toggle buka/tutup dropdown
        btnNotif.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                // Refresh data saat dropdown dibuka
                fetchNotifikasiKasir();
            }
        });

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !btnNotif.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Cegah penutupan saat mengklik di dalam dropdown
        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // Jalankan polling awal & berkala
        fetchNotifikasiKasir();
        setInterval(fetchNotifikasiKasir, POLL_INTERVAL);
    }

    async function fetchNotifikasiKasir() {
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
                updateNotifikasiUI(data.count || 0, data.pasien || []);
            }
        } catch (err) {
            console.warn('[SIMRS] Gagal memeriksa notifikasi antrean kasir:', err);
        }
    }

    function updateNotifikasiUI(count, pasienList) {
        // 1. Update Badge Topbar
        const badgeCount = document.getElementById('topbar-notif-count');
        const pulseDot   = document.getElementById('topbar-notif-pulse');
        const headerCount= document.getElementById('dropdown-header-count');
        const listBody   = document.getElementById('dropdown-notif-list');
        const sidebarPill= document.getElementById('sidebar-notif-count');

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

        // 2. Render List di Popover Dropdown
        if (listBody) {
            if (count === 0) {
                listBody.innerHTML = `
                    <div class="notif-empty-state">
                        <i class="fa-solid fa-circle-check"></i>
                        <p style="margin:0; font-weight:600; color:#1e293b;">Semua Antrean Selesai</p>
                        <p style="margin:4px 0 0; font-size:11.5px;">Tidak ada pasien yang menunggu pembayaran saat ini.</p>
                    </div>
                `;
            } else {
                let html = '';
                // Batasi maksimal 5 antrean teratas agar popover tetap bersih dan tidak penuh
                const tampilPasien = pasienList.slice(0, 5);
                tampilPasien.forEach(p => {
                    html += `
                        <div class="notif-item">
                            <div class="notif-item-info">
                                <div class="notif-item-name">
                                    <span>${escapeHtml(p.nama_pasien)}</span>
                                    <span class="notif-item-antrian">${escapeHtml(p.no_antrian)}</span>
                                </div>
                                <div class="notif-item-meta">
                                    <span><i class="fa-solid fa-id-card"></i> ${escapeHtml(p.no_rm)}</span>
                                    <span>•</span>
                                    <span><i class="fa-solid fa-clinic-medical"></i> ${escapeHtml(p.nama_poli)}</span>
                                </div>
                            </div>
                            <a href="${p.url_layani}" class="notif-btn-layani" title="Layani pembayaran pasien ini sekarang">
                                Layani <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
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

        // 3. Deteksi Pasien Baru untuk Audio Chime & Floating Toast
        const currentIds = new Set(pasienList.map(p => p.id_pendaftaran));
        const newPatients = pasienList.filter(p => !knownPatientIds.has(p.id_pendaftaran));

        if (!isFirstRun && newPatients.length > 0) {
            // Bunyikan chime notifikasi lembut
            playHospitalChime();

            // Tampilkan floating toast untuk pasien baru yang masuk
            newPatients.forEach(p => {
                showToastNotifikasi(p);
            });
        }

        // Update tracking state
        knownPatientIds = currentIds;
        lastKnownCount = count;
        isFirstRun = false;
    }

    /**
     * Synthesized Melodic Hospital Chime (Tanpa file audio eksternal)
     * Menggunakan Web Audio API: 2 nada harmonis (D5 -> A5) khas RS
     */
    function playHospitalChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;

            const ctx = new AudioContext();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            const now = ctx.currentTime;

            // Nada 1: D5 (587.33 Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.18, now + 0.03);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            // Nada 2: A5 (880 Hz) - Berbunyi sedikit setelah nada 1
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880, now + 0.12);
            gain2.gain.setValueAtTime(0, now + 0.12);
            gain2.gain.linearRampToValueAtTime(0.15, now + 0.15);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.65);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.65);
        } catch (e) {
            // Browser mungkin memblokir audio sebelum interaksi pengguna
        }
    }

    /**
     * Menampilkan Floating Toast Card di pojok kanan bawah
     */
    function showToastNotifikasi(pasien) {
        let container = document.getElementById('simrs-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'simrs-toast-container';
            container.className = 'simrs-toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'simrs-toast';
        toast.innerHTML = `
            <div class="simrs-toast-icon">
                <i class="fa-solid fa-bell-concierge"></i>
            </div>
            <div class="simrs-toast-content">
                <div class="simrs-toast-title">Pasien Siap Bayar!</div>
                <div class="simrs-toast-msg">
                    <strong>${escapeHtml(pasien.nama_pasien)}</strong> (${escapeHtml(pasien.no_antrian)}) telah selesai di <strong>${escapeHtml(pasien.nama_poli)}</strong> dan menunggu di kasir.
                </div>
                <a href="${pasien.url_layani}" class="simrs-toast-btn">
                    Layani Kasir <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <button type="button" class="simrs-toast-close" title="Tutup">&times;</button>
        `;

        const closeBtn = toast.querySelector('.simrs-toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                dismissToast(toast);
            });
        }

        container.appendChild(toast);

        // Auto dismiss setelah 7 detik
        setTimeout(function () {
            dismissToast(toast);
        }, 7000);
    }

    function dismissToast(toast) {
        if (!toast || !toast.parentNode) return;
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(40px) scale(0.9)';
        setTimeout(function () {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
})();

<!-- MODAL PERMOHONAN BANTUAN RESET PASSWORD & 2FA KE ADMINISTRATOR RS -->
<div id="modal-permintaan-reset" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <!-- Backdrop Blur -->
    <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(5px); transition: opacity 0.3s;" onclick="tutupModalPermintaanReset()"></div>

    <!-- Modal Card Box -->
    <div style="position: relative; z-index: 10; width: 100%; max-width: 520px; background: #ffffff; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3); border: 1px solid #e2e8f0; overflow: hidden; animation: zoomInResetModal 0.25s ease-out;">
        
        <!-- Modal Header -->
        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #f8fafc 0%, #f0f6fd 100%);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">Bantuan Akun & Reset Akses</h3>
                    <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Hubungi Administrator IT Rumah Sakit Delta</p>
                </div>
            </div>
            <button type="button" onclick="tutupModalPermintaanReset()" 
                    style="width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                    onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a';"
                    onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form action="{{ route('bantuan.permintaan-reset') }}" method="POST" style="padding: 24px;">
            @csrf

            <!-- Info Alert Box -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 12px 14px; margin-bottom: 18px; display: flex; gap: 10px; font-size: 11.5px; color: #1e40af; line-height: 1.45;">
                <i class="fa-solid fa-circle-info" style="font-size: 16px; color: #3b82f6; flex-shrink: 0; margin-top: 1px;"></i>
                <div>
                    Permintaan ini akan diteruskan ke <strong>Administrator RS Delta</strong>. Setelah diverifikasi dan diproses, informasi kredensial baru / konfirmasi reset akan dikirimkan langsung ke <strong>nomor WhatsApp/HP</strong> akun Anda.
                </div>
            </div>

            <!-- Input Identitas (NIK / Username / RM) -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                    NIK / Username / Nomor RM *
                </label>
                <div style="position: relative;">
                    <i class="fa-regular fa-id-card" style="position: absolute; left: 14px; top: 12px; color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" name="identitas" placeholder="Masukkan NIK, Username, atau No. RM Anda" required
                           style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 13px; color: #0f172a; box-sizing: border-box; outline: none; transition: all 0.2s;"
                           onfocus="this.style.borderColor='#1a4a8d'; this.style.boxShadow='0 0 0 3px rgba(26, 74, 141, 0.15)';"
                           onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';">
                </div>
            </div>

            <!-- Pilihan Tipe Permintaan Reset -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                    Pilih Jenis Kendala Akun *
                </label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <label style="cursor: pointer;">
                        <input type="radio" name="tipe_permintaan" value="password" checked style="display: none;" onchange="updateRadioSelection(this)">
                        <div class="radio-card-option selected-option" id="opt-password" style="padding: 10px 8px; border-radius: 10px; border: 1.5px solid #1a4a8d; background: #f0f7ff; text-align: center; transition: all 0.2s;">
                            <i class="fa-solid fa-key" style="font-size: 16px; color: #1a4a8d; display: block; margin-bottom: 4px;"></i>
                            <strong style="display: block; font-size: 11px; color: #1a4a8d;">Lupa Password</strong>
                            <span style="display: block; font-size: 9.5px; color: #64748b;">Kirim Sandi Baru</span>
                        </div>
                    </label>

                    <label style="cursor: pointer;">
                        <input type="radio" name="tipe_permintaan" value="2fa" style="display: none;" onchange="updateRadioSelection(this)">
                        <div class="radio-card-option" id="opt-2fa" style="padding: 10px 8px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #ffffff; text-align: center; transition: all 0.2s;">
                            <i class="fa-solid fa-shield-halved" style="font-size: 16px; color: #64748b; display: block; margin-bottom: 4px;"></i>
                            <strong style="display: block; font-size: 11px; color: #334155;">Reset 2FA</strong>
                            <span style="display: block; font-size: 9.5px; color: #64748b;">Ganti HP / Hilang</span>
                        </div>
                    </label>

                    <label style="cursor: pointer;">
                        <input type="radio" name="tipe_permintaan" value="keduanya" style="display: none;" onchange="updateRadioSelection(this)">
                        <div class="radio-card-option" id="opt-keduanya" style="padding: 10px 8px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #ffffff; text-align: center; transition: all 0.2s;">
                            <i class="fa-solid fa-lock-open" style="font-size: 16px; color: #64748b; display: block; margin-bottom: 4px;"></i>
                            <strong style="display: block; font-size: 11px; color: #334155;">Keduanya</strong>
                            <span style="display: block; font-size: 9.5px; color: #64748b;">Password & 2FA</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Input No HP Darurat / Pemohon (Opsional untuk konfirmasi) -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                    Nomor WhatsApp / HP Aktif Pemohon
                    <span style="font-size: 10.5px; font-weight: normal; color: #64748b;">(Untuk menerima info reset)</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-brands fa-whatsapp" style="position: absolute; left: 14px; top: 12px; color: #16a34a; font-size: 15px;"></i>
                    <input type="text" name="no_hp_darurat" placeholder="Contoh: 081234567890"
                           style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 13px; color: #0f172a; box-sizing: border-box; outline: none; transition: all 0.2s;"
                           onfocus="this.style.borderColor='#1a4a8d'; this.style.boxShadow='0 0 0 3px rgba(26, 74, 141, 0.15)';"
                           onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';">
                </div>
            </div>

            <!-- Keterangan / Alasan -->
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                    Alasan / Catatan Kendala
                    <span style="font-size: 10.5px; font-weight: normal; color: #64748b;">(Opsional)</span>
                </label>
                <textarea name="catatan" rows="2" placeholder="Contoh: HP saya ganti dan aplikasi Google Authenticator terhapus, mohon bantuan reset..."
                          style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 12px; color: #0f172a; box-sizing: border-box; outline: none; resize: none; transition: all 0.2s;"
                          onfocus="this.style.borderColor='#1a4a8d'; this.style.boxShadow='0 0 0 3px rgba(26, 74, 141, 0.15)';"
                          onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"></textarea>
            </div>

            <!-- Actions Button -->
            <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 14px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="tutupModalPermintaanReset()"
                        style="padding: 10px 18px; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9';"
                        onmouseout="this.style.background='#ffffff';">
                    Batal
                </button>
                <button type="submit"
                        style="padding: 10px 22px; border-radius: 10px; border: none; background: #1a4a8d; color: #ffffff; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(26, 74, 141, 0.3); transition: all 0.2s;"
                        onmouseover="this.style.background='#143a6d'; this.style.transform='translateY(-1px)';"
                        onmouseout="this.style.background='#1a4a8d'; this.style.transform='translateY(0)';">
                    <i class="fa-solid fa-paper-plane"></i> Kirim ke Admin RS
                </button>
            </div>
        </form>

    </div>
</div>

<style>
@keyframes zoomInResetModal {
    from {
        opacity: 0;
        transform: scale(0.94);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<script>
function bukaModalPermintaanReset() {
    const modal = document.getElementById('modal-permintaan-reset');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function tutupModalPermintaanReset() {
    const modal = document.getElementById('modal-permintaan-reset');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function updateRadioSelection(radioEl) {
    const val = radioEl.value;
    const cards = ['password', '2fa', 'keduanya'];
    cards.forEach(c => {
        const el = document.getElementById('opt-' + c);
        if (el) {
            if (c === val) {
                el.style.borderColor = '#1a4a8d';
                el.style.background = '#f0f7ff';
                const title = el.querySelector('strong');
                if (title) title.style.color = '#1a4a8d';
                const icon = el.querySelector('i');
                if (icon) icon.style.color = '#1a4a8d';
            } else {
                el.style.borderColor = '#e2e8f0';
                el.style.background = '#ffffff';
                const title = el.querySelector('strong');
                if (title) title.style.color = '#334155';
                const icon = el.querySelector('i');
                if (icon) icon.style.color = '#64748b';
            }
        }
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupModalPermintaanReset();
    }
});
</script>

@php
    $activeMenu = $active ?? 'dashboard';
@endphp

<!-- NAVBAR RESMI PASIEN RS DELTA (STANDAR TERPADU) -->
<nav class="bg-gradient-to-r from-[#1a4a8d] via-[#2a5fa5] to-[#4D79C1] text-white shadow-lg sticky top-0 z-40 w-full">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20 gap-4">
            
            <!-- 1. LOGO & BRANDING RS DELTA (FIXED / FLEX-SHRINK-0) -->
            <a href="{{ route('pasien.dashboard') }}" class="flex items-center gap-3 flex-shrink-0 group">
                <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo Delta" class="w-10 h-10 object-contain drop-shadow group-hover:scale-105 transition-transform">
                <div class="flex-shrink-0">
                    <span class="text-base sm:text-lg font-bold block leading-tight tracking-wide text-white">Rumah Sakit Delta</span>
                    <span class="text-[11px] text-blue-100 font-light block leading-none mt-0.5">Portal Pasien Terpadu</span>
                </div>
            </a>

            <!-- 2. NAVIGATION TABS (CENTERED / WHITESPACE-NOWRAP / SINGLE ROW) -->
            <div class="hidden md:flex items-center gap-1.5 lg:gap-2 flex-shrink-0">
                <a href="{{ route('pasien.dashboard') }}" 
                   class="px-3.5 py-2 rounded-2xl text-xs whitespace-nowrap transition-all flex items-center gap-1.5 flex-shrink-0 {{ $activeMenu === 'dashboard' ? 'font-bold bg-white text-[#1a4a8d] shadow-sm' : 'font-medium text-white/85 hover:text-white hover:bg-white/10' }}">
                    <i class="fa-solid fa-house text-[11px]"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pasien.riwayat') }}" 
                   class="px-3.5 py-2 rounded-2xl text-xs whitespace-nowrap transition-all flex items-center gap-1.5 flex-shrink-0 {{ $activeMenu === 'riwayat' ? 'font-bold bg-white text-[#1a4a8d] shadow-sm' : 'font-medium text-white/85 hover:text-white hover:bg-white/10' }}">
                    <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                    <span>Riwayat Medis</span>
                </a>
                <a href="{{ route('pasien.komparasi') }}" 
                   class="px-3.5 py-2 rounded-2xl text-xs whitespace-nowrap transition-all flex items-center gap-1.5 flex-shrink-0 {{ $activeMenu === 'komparasi' ? 'font-bold bg-white text-[#1a4a8d] shadow-sm' : 'font-medium text-white/85 hover:text-white hover:bg-white/10' }}">
                    <i class="fa-solid fa-code-compare text-[11px]"></i>
                    <span>Bandingkan Berobat</span>
                </a>
                <button type="button" onclick="bukaModalKartuPasien()"
                   class="px-3.5 py-2 rounded-2xl text-xs whitespace-nowrap transition-all flex items-center gap-1.5 flex-shrink-0 {{ $activeMenu === 'kartu' ? 'font-bold bg-white text-[#1a4a8d] shadow-sm' : 'font-medium text-white/85 hover:text-white hover:bg-white/10' }}"
                   title="Lihat Pratinjau & Cetak Kartu Berobat">
                    <i class="fa-solid fa-id-card text-[11px]"></i>
                    <span>Kartu Pasien</span>
                </button>
            </div>

            <!-- 3. USER PROFILE & LOGOUT (FIXED ALIGNMENT / SINGLE ROW) -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <button type="button" onclick="bukaModalEditProfilPasien()"
                        class="flex items-center gap-3 flex-shrink-0 text-left group hover:opacity-95 transition cursor-pointer p-1 -m-1 rounded-2xl hover:bg-white/10"
                        title="Klik untuk melihat & mengedit profil akun Anda">
                    <div class="relative">
                        <img src="{{ $pasien->foto_url ?? asset('images/avatar_male.jpg') }}" alt="Avatar Pasien" 
                             class="w-10 h-10 rounded-full object-cover border-2 border-white/60 shadow group-hover:border-white transition flex-shrink-0">
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-[#1a4a8d] text-white border border-white text-[8px] flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-pen"></i>
                        </span>
                    </div>
                    <div class="text-right hidden sm:block leading-tight">
                        <span class="block text-xs font-bold text-white max-w-[130px] truncate group-hover:text-blue-100 transition">{{ $pasien->nama_pasien ?? session('nama_pasien') }}</span>
                        <span class="block text-[10px] text-blue-200 font-mono mt-0.5">No. RM: {{ $pasien->rm ?? session('rm') }}</span>
                    </div>
                </button>
                <a href="{{ route('logout') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs border border-white/20 transition flex-shrink-0 shadow-sm"
                   title="Keluar dari Portal Pasien">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </a>
            </div>

        </div>
    </div>
</nav>

@php
    $pData = $pasien ?? \App\Models\Pasien::find(session('id_pasien'));
    $barcodeSvgVektor = $pData ? \App\Services\BarcodeService::generateCode39Svg($pData->rm, 34, 1.8) : '';
@endphp

<!-- MODAL PREVIEW KARTU PASIEN (STANDAR CR80) -->
<div id="modal-kartu-pasien" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="tutupModalKartuPasien()"></div>

    <!-- Modal Content -->
    <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4 sm:p-6">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 transform transition-all">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Pratinjau Kartu Berobat Pasien</h3>
                        <p class="text-xs text-slate-500">Standar Fisik CR80 (85.6 × 53.98 mm) • Rumah Sakit Delta</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalKartuPasien()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition" title="Tutup">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Tab Sisi Depan / Sisi Belakang -->
            <div class="flex items-center justify-center gap-2 my-4">
                <button type="button" id="tab-depan-btn" onclick="pilihSisiKartu('depan')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm bg-[#1a4a8d] text-white flex items-center gap-1.5">
                    <i class="fa-solid fa-address-card"></i> Sisi Depan (Identitas)
                </button>
                <button type="button" id="tab-belakang-btn" onclick="pilihSisiKartu('belakang')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved"></i> Sisi Belakang (Ketentuan)
                </button>
            </div>

            <!-- Container Kartu CR80 -->
            <div class="flex justify-center my-2">
                @if ($pData)
                    <!-- SISI DEPAN -->
                    <div id="kartu-sisi-depan" class="w-full max-w-[400px] aspect-[1.586/1] rounded-2xl p-4 sm:p-5 text-slate-800 shadow-xl border border-sky-200 relative overflow-hidden flex flex-col justify-between"
                         style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 60%, #e0f2fe 100%);">
                        
                        <!-- Top Accent Bar -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#1a4a8d]"></div>
                        
                        <!-- Header Kartu -->
                        <div class="flex items-center justify-between pb-2 border-b border-sky-300">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/logodelta1.svg') }}" alt="Logo" class="w-7 h-7 object-contain">
                                <div>
                                    <h4 class="text-xs font-extrabold text-[#1a4a8d] tracking-wide uppercase leading-tight">Rumah Sakit Delta</h4>
                                    <span class="text-[8px] font-bold text-sky-700 tracking-wider uppercase block">Kartu Pasien Rawat Jalan</span>
                                </div>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Aktif
                            </span>
                        </div>

                        <!-- Body Kartu: Avatar & Identitas -->
                        <div class="flex gap-3 my-1.5 items-center">
                            <div class="w-14 h-16 rounded-xl border border-sky-300 bg-white overflow-hidden shadow-sm flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $pData->foto_url ?? asset('images/avatar_male.jpg') }}" alt="Foto" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0 text-left">
                                <div class="text-xs font-extrabold text-slate-900 truncate">{{ $pData->nama_pasien ?? 'Nama Pasien' }}</div>
                                <div class="inline-block bg-[#1a4a8d] text-white font-mono text-[11px] font-bold px-2 py-0.5 rounded my-0.5 tracking-wider shadow-sm">
                                    {{ $pData->rm ?? 'RM-0000' }}
                                </div>
                                <div class="text-[10px] text-slate-600 space-y-0.5 leading-snug">
                                    <div><span class="text-slate-400 font-medium">NIK:</span> <span class="font-semibold">{{ $pData->nik ?? '-' }}</span></div>
                                    <div><span class="text-slate-400 font-medium">Tgl Lahir:</span> <span class="font-semibold">{{ $pData->tanggal_lahir ? \Carbon\Carbon::parse($pData->tanggal_lahir)->translatedFormat('d M Y') : '-' }} ({{ ($pData->jenis_kelamin ?? 'L') === 'P' ? 'P' : 'L' }})</span></div>
                                    <div class="truncate"><span class="text-slate-400 font-medium">Alamat:</span> <span class="font-semibold">{{ $pData->alamat ?? '-' }}</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Kartu: Barcode Code 39 SVG -->
                        <div class="bg-white rounded-lg p-1 border border-sky-200 flex flex-col items-center justify-center">
                            <div class="w-full flex justify-center overflow-hidden max-h-6">
                                {!! $barcodeSvgVektor !!}
                            </div>
                            <span class="text-[8px] font-mono font-bold tracking-widest text-slate-700">{{ $pData->rm ?? 'RM-0000' }}</span>
                        </div>
                    </div>

                    <!-- SISI BELAKANG -->
                    <div id="kartu-sisi-belakang" class="hidden w-full max-w-[400px] aspect-[1.586/1] rounded-2xl p-4 sm:p-5 text-white shadow-xl border border-slate-700 relative overflow-hidden flex flex-col justify-between"
                         style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-sky-400"></div>

                        <div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-700">
                                <span class="text-[11px] font-bold text-sky-400 uppercase tracking-wider">Ketentuan Kartu Berobat</span>
                                <span class="text-[9px] text-slate-400 font-mono">RS DELTA</span>
                            </div>
                            <ul class="text-[10px] text-slate-300 mt-2.5 space-y-1 list-disc list-inside leading-snug">
                                <li>Kartu ini adalah bukti pendaftaran resmi pasien di RS Delta.</li>
                                <li>Wajib dibawa setiap kali berkunjung berobat ke poliklinik / IGD.</li>
                                <li>Kartu tidak dapat dipindahtangankan kepada orang lain.</li>
                                <li>Bila hilang atau rusak, silakan lapor ke Loket Pendaftaran.</li>
                            </ul>
                        </div>

                        <div class="pt-2 border-t border-slate-700 flex items-center justify-between text-[9px] text-slate-400">
                            <div class="flex items-center gap-1 text-sky-300">
                                <i class="fa-solid fa-headset"></i>
                                <span>IGD 24 Jam: (0511) 330-1122</span>
                            </div>
                            <span>Banjarbaru, Kalsel</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Footer: Actions -->
            <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-[11px] text-slate-400 text-center sm:text-left">
                    <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Format fisik siap cetak
                </span>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="tutupModalKartuPasien()"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Tutup
                    </button>
                    <a href="{{ route('pasien.kartu') }}" target="_blank"
                       class="px-5 py-2.5 rounded-xl bg-[#1a4a8d] hover:bg-[#143869] text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Kartu Berobat</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function bukaModalKartuPasien() {
        const modal = document.getElementById('modal-kartu-pasien');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function tutupModalKartuPasien() {
        const modal = document.getElementById('modal-kartu-pasien');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function pilihSisiKartu(sisi) {
        const depan = document.getElementById('kartu-sisi-depan');
        const belakang = document.getElementById('kartu-sisi-belakang');
        const tabDepan = document.getElementById('tab-depan-btn');
        const tabBelakang = document.getElementById('tab-belakang-btn');

        if (!depan || !belakang || !tabDepan || !tabBelakang) return;

        if (sisi === 'depan') {
            depan.classList.remove('hidden');
            belakang.classList.add('hidden');
            tabDepan.className = 'px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm bg-[#1a4a8d] text-white flex items-center gap-1.5';
            tabBelakang.className = 'px-4 py-2 rounded-xl text-xs font-semibold transition bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center gap-1.5';
        } else {
            depan.classList.add('hidden');
            belakang.classList.remove('hidden');
            tabBelakang.className = 'px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm bg-[#1a4a8d] text-white flex items-center gap-1.5';
            tabDepan.className = 'px-4 py-2 rounded-xl text-xs font-semibold transition bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center gap-1.5';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalKartuPasien();
            tutupModalEditProfilPasien();
        }
    });

    function bukaModalEditProfilPasien() {
        const modal = document.getElementById('modal-edit-profil-pasien');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function tutupModalEditProfilPasien() {
        const modal = document.getElementById('modal-edit-profil-pasien');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
</script>

<!-- MODAL EDIT PROFIL & AKUN PASIEN -->
<div id="modal-edit-profil-pasien" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="tutupModalEditProfilPasien()"></div>

    <!-- Modal Content -->
    <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4 sm:p-6">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 transform transition-all">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1a4a8d] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Pengaturan Profil & Akun Pasien</h3>
                        <p class="text-xs text-slate-500">Kelola identitas kontak dan kredensial akun portal Anda</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalEditProfilPasien()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition" title="Tutup">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Profile Avatar Card & Gender Clarification Note -->
            @if ($pData)
                <div class="my-5 p-4 rounded-2xl bg-gradient-to-r from-blue-50/80 to-slate-50 border border-blue-100 flex items-center gap-4">
                    <div class="relative flex-shrink-0">
                        <img src="{{ $pData->foto_url ?? asset('images/avatar_male.jpg') }}" alt="Avatar" 
                             class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                        <span class="absolute bottom-0 right-0 w-5 h-5 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[9px] {{ ($pData->jenis_kelamin ?? 'L') === 'P' ? 'text-pink-500' : 'text-blue-500' }}">
                            <i class="fa-solid {{ ($pData->jenis_kelamin ?? 'L') === 'P' ? 'fa-venus' : 'fa-mars' }}"></i>
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-800">Foto Profil Otomatis</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($pData->jenis_kelamin ?? 'L') === 'P' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ($pData->jenis_kelamin ?? 'L') === 'P' ? 'Perempuan' : 'Laki-laki' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                            <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i>
                            Foto avatar ditentukan otomatis oleh sistem berdasarkan <strong>Jenis Kelamin ({{ ($pData->jenis_kelamin ?? 'L') === 'P' ? 'Perempuan' : 'Laki-laki' }})</strong> yang terdaftar di rekam medis demi integritas data pasien RS Delta.
                        </p>
                    </div>
                </div>

                <form action="{{ route('pasien.profil.update') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Identitas Medis Resmi (READ-ONLY) -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                                No. Rekam Medis (RM) <i class="fa-solid fa-lock text-slate-400 text-[9px]"></i>
                            </label>
                            <input type="text" value="{{ $pData->rm }}" readonly
                                   class="w-full px-3 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-xs font-mono font-bold cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                                NIK KTP <i class="fa-solid fa-lock text-slate-400 text-[9px]"></i>
                            </label>
                            <input type="text" value="{{ $pData->nik }}" readonly
                                   class="w-full px-3 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-xs font-mono font-semibold cursor-not-allowed">
                        </div>
                    </div>

                    <!-- DATA EDITABLE -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Pasien *</label>
                        <input type="text" name="nama_pasien" value="{{ old('nama_pasien', $pData->nama_pasien) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 bg-white">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Username Login</label>
                            <input type="text" name="username" value="{{ old('username', $pData->username) }}"
                                   placeholder="Username portal"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $pData->no_hp) }}"
                                   placeholder="Contoh: 08123456789"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat" rows="2" placeholder="Alamat lengkap tempat tinggal"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 bg-white">{{ old('alamat', $pData->alamat) }}</textarea>
                    </div>

                    <!-- GANTI PASSWORD (OPSIONAL) -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-key text-[#1a4a8d] text-xs"></i>
                            <span class="text-xs font-bold text-slate-700">Ubah Password Akun (Opsional)</span>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-snug">Kosongkan jika tidak ingin mengubah password akun portal Anda.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-medium text-slate-600 mb-1">Password Baru</label>
                                <input type="password" name="password" placeholder="Minimal 4 karakter"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-400 bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-slate-600 mb-1">Ulangi Password</label>
                                <input type="password" name="password_confirmation" placeholder="Ketik ulang password"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-400 bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="tutupModalEditProfilPasien()"
                                class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-[#1a4a8d] hover:bg-[#143869] text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>

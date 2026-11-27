# 🏥 SIMRS Delta — Sistem Informasi Manajemen Rumah Sakit Terpadu
### *Integrated Enterprise Hospital Management System & Patient Mobile Backend*

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Sanctum](https://img.shields.io/badge/API-Laravel_Sanctum-blue?style=for-the-badge)](https://laravel.com/docs/11.x/sanctum)
[![Tests](https://img.shields.io/badge/Tests-103_Passed_(838_Assertions)-success?style=for-the-badge&logo=githubactions&logoColor=white)](#pengujian-otomatis-automated-testing)

---

## 📌 Ringkasan Proyek (Overview)

**SIMRS Delta** adalah aplikasi Sistem Informasi Manajemen Rumah Sakit (SIMRS) berbasis web dan REST API yang dirancang untuk mengotomatisasi dan mengintegrasikan seluruh siklus alur pelayanan rawat jalan rumah sakit secara *end-to-end* (mulai dari pendaftaran, skrining tanda vital, pemeriksaan dokter, peracikan obat farmasi, billing kasir, hingga portal mandiri pasien dan integrasi mobile).

Dibangun dengan arsitektur modular menggunakan **Laravel 11**, sistem ini dilengkapi keamanan **Two-Factor Authentication (TOTP Google Authenticator)**, notifikasi real-time (*polling & audio chime*), sistem pemanggilan suara antrean ruang tunggu otomatis (*Text-to-Speech*), kartu pasien digital standar **CR80**, serta pengujian otomatis (*automated testing*) dengan tingkat ketahanan tinggi (**103 pengujian sukses**).

---

## 🚀 Fitur Unggulan & Alur Pelayanan (Core Workflows)

### 1. 🌐 Portal Pasien Terpadu (*Patient Self-Service Portal*)
- **Pendaftaran & Login Mandiri**: Pasien dapat mendaftar dan masuk menggunakan NIK, Nomor Rekam Medis (RM), atau Username.
- **Dashboard Pasien**: Menampilkan status antrean aktif hari ini, panduan 4 alur poliklinik, dan ringkasan 3 kunjungan medis terakhir.
- **Kartu Pasien Digital CR80**: Kartu identitas pasien digital interaktif dengan pratinjau sisi depan (*front*) dan belakang (*back*), barcode No. RM, serta tombol unduh/cetak fisik.
- **Riwayat Rekam Medis & Kuitansi**: Rincian diagnosa dokter, tindakan klinis, catatan resep obat, dan kuitansi pembayaran resmi.
- **Komparasi Kunjungan Medis**: Pasien dapat membandingkan perkembangan kesehatan antar kunjungan (tekanan darah sistol/diastol, suhu tubuh, berat badan, serta perubahan terapi obat).
- **Pengaturan Profil**: Pasien dapat memperbarui nomor WhatsApp, alamat domisili, dan kata sandi (dengan penguncian proteksi pada NIK, No. RM, dan jenis kelamin).

### 2. 🎫 Loket Pendaftaran & Admisi Pasien
- **Pendaftaran Pasien Walk-In**: Registrasi pasien baru dengan penomoran otomatis No. Rekam Medis (`RM-XXXX`) serta pencarian cepat pasien lama.
- **Penerbitan Antrean Poliklinik**: Pemilihan poli tujuan, dokter yang bertugas, keluhan awal, dan cetak karcis antrean lengkap dengan barcode/QR.
- **Cetak Kartu Fisik Pasien**: Penerbitan kartu berobat standar rumah sakit langsung dari loket.

### 3. 🩺 Skrining Tanda Vital (TTV) — Perawat
- **Antrean Real-time**: Daftar antrean pasien yang menunggu pengukuran tanda-tanda vital dari loket.
- **Pencatatan TTV Lengkap**: Tekanan darah (sistol/diastol), frekuensi nadi, suhu tubuh, laju napas, berat badan, tinggi badan, skala nyeri, dan keluhan utama.
- **Notifikasi Lonceng & Suara**: Lonceng topbar interaktif dan lonceng audio yang berbunyi saat ada pasien baru masuk.

### 4. 👨‍⚕️ Pelayanan Medis & E-Resep — Dokter Spesialis
- **Antrean Pasien Siap Periksa**: Pemanggilan pasien yang telah lolos skrining TTV.
- **Akses Rekam Medis Masa Lalu**: Dokter dapat meninjau riwayat kunjungan dan diagnosa masa lalu pasien dalam satu layar.
- **Input Diagnosa & Tindakan**: Diagnosa ICD-10, anamnesis, tindakan klinis, serta penerbitan E-Resep elektronik terhubung langsung ke bagian farmasi.
- **Cetak Dokumen Medis**: Pembuatan dan cetak dokumen **Resume Medis Rawat Jalan** dan **Surat Keterangan Sakit** resmi berformat PDF.

### 5. 💊 Instalasi Farmasi & Apotek — Apoteker
- **Antrean E-Resep Real-time**: Deteksi resep masuk seketika setelah diselesaikan oleh dokter.
- **Telaah & Verifikasi Resep**: Verifikasi prinsip 5 Benar (Pasien, Obat, Dosis, Waktu, Cara Pemberian).
- **Cetak Etiket Obat**: Cetak label aturan pakai obat untuk ditempelkan pada kemasan obat pasien.
- **Manajemen Inventori & Stok Obat**: Katalog obat, stok masuk/keluar, peringatan stok menipis (*low-stock alert*), dan cetak laporan inventori obat PDF.
- **Kontrol Penyerahan Obat**: Proteksi sistem di mana obat hanya dapat diserahkan setelah tagihan diselesaikan di kasir.

### 6. 💳 Kasir & Billing Pembayaran
- **Kalkulasi Billing Otomatis**: Perhitungan otomatis biaya pendaftaran poli, tindakan medis dokter, dan rincian obat dari farmasi.
- **Multi-Metode Pembayaran**: Mendukung Tunai (dengan kalkulator kembalian), QRIS, Transfer Bank, dan Kartu Debit/Kredit.
- **Cetak Struk Resmi**: Penerbitan bukti bayar/kuitansi kasir berformat struk thermal dan dokumen PDF.
- **Laporan Finansial**: Rekapitulasi transaksi harian/bulanan dengan fitur ekspor ke **PDF** dan **Excel**.

### 7. 📺 Monitoring Antrean & Layar Display TV — Resepsionis
- **Journey Tracker**: Pemantauan alur perjalanan pasien dari loket hingga apotek/kasir secara terpadu.
- **Display TV Antrean**: Tampilan layar lebar TV ruang tunggu yang memuat nomor antrean yang sedang dipanggil per poliklinik.
- **Panggilan Suara Otomatis (TTS)**: Pemanggilan nomor antrean menggunakan suara sintesis (*Text-to-Speech*) berbahasa Indonesia ("*Nomor antrian UMU-001 silakan menuju Poli Umum*").

### 8. 🛡️ Super Administrator RS
- **Dashboard Eksekutif**: Grafik statistik kunjungan, rasio pasien baru vs lama, utilisasi poli, dan cetak laporan eksekutif PDF.
- **Manajemen Staf & Pengguna**: Pendaftaran pegawai, penempatan poliklinik dokter, reset password, dan status proteksi 2FA.
- **Menu Permintaan Reset Password & 2FA**: Halaman khusus penerimaan permohonan pemulihan akun dari pengguna yang terkunci, generator password baru, serta pengiriman pesan instan satu klik via **WhatsApp**.
- **Master Data**: Kelola tarif layanan medis, poliklinik, dan ruangan.
- **Cadangan Database (Backup)**: Download instan cadangan `.sql`, pembuatan file arsip zip, dan penghapusan backup lama.

---

## 📱 Arsitektur RESTful API (Khusus Aplikasi Mobile Pasien)

Sistem telah dilengkapi modul **REST API V1** yang diotentikasi menggunakan **Laravel Sanctum** (*Bearer Token*) sehingga siap langsung diintegrasikan dengan aplikasi mobile (Flutter, React Native, Kotlin, Swift):

| Method | Endpoint | Fungsi |
| :--- | :--- | :--- |
| `POST` | `/api/v1/auth/login` | Login Pasien (Mendukung NIK, No. RM, atau Username) |
| `POST` | `/api/v1/auth/register-pasien` | Pendaftaran akun baru pasien secara mandiri |
| `GET` | `/api/v1/auth/me` | Mengambil data profil akun yang sedang login |
| `POST` | `/api/v1/auth/logout` | Revoke token autentikasi Sanctum |
| `POST` | `/api/v1/auth/lupa-password` | Pengajuan bantuan reset password jika terkunci |
| `GET` | `/api/v1/mobile/jadwal-poli` | Melihat daftar poliklinik, biaya, dan antrean hari ini |
| `POST` | `/api/v1/mobile/daftar-online` | Mengambil nomor antrean poliklinik dari HP |
| `GET` | `/api/v1/mobile/tiket-antrian-aktif` | Cek status antrean aktif & nomor yang sedang dipanggil |
| `GET` | `/api/v1/mobile/kartu-pasien` | Data kartu digital pasien CR80 untuk aplikasi mobile |
| `GET` | `/api/v1/mobile/riwayat-kunjungan` | Riwayat rekam medis pasien (*paginated*) |
| `GET` | `/api/v1/mobile/riwayat-kunjungan/{id}` | Detail lengkap 1 kunjungan (TTV, diagnosa, resep, billing) |
| `GET` | `/api/v1/mobile/komparasi` | Data perbandingan kesehatan antar kunjungan |
| `PUT` | `/api/v1/mobile/profil` | Update data profil & ubah password pasien |

---

## 🔒 Fitur Keamanan Sistem (Security Architecture)

1. **Two-Factor Authentication (TOTP 2FA)**: Terintegrasi dengan aplikasi autentikator standar industri seperti *Google Authenticator*, *Microsoft Authenticator*, dan *Authy*.
2. **Role-Based Access Control (RBAC)**: Pembatasan hak akses berbasis middleware kustom (`role:admin`, `role:dokter`, `role:perawat`, `role:kasir`, `role:apoteker`, `role:loket`, `role:resepsionis`).
3. **Data Sanitization & Injection Defense**: Parameterized queries via Eloquent ORM, proteksi CSRF (*Cross-Site Request Forgery*) di seluruh form, dan hashing kata sandi berbasis algoritma **Bcrypt**.

---

## 🧪 Pengujian Otomatis (Automated Testing)

Proyek ini menerapkan standar *Test-Driven Development (TDD)* yang ketat menggunakan PHPUnit. Seluruh pengujian mencakup skenario otentikasi, alur pelayanan multi-pos, permission guard, hingga REST API mobile.

```bash
php artisan test
```

**Hasil Pengujian:**
```text
   PASS  Tests\Feature\AdminEksekutifDanKartuTest
   PASS  Tests\Feature\ApotekerWebTest
   PASS  Tests\Feature\DokterWebTest
   PASS  Tests\Feature\LoketDanPasienTest
   PASS  Tests\Feature\MobilePatientApiTest
   PASS  Tests\Feature\PermintaanResetTest
   PASS  Tests\Feature\ResepsionisWebTest
   PASS  Tests\Feature\SimrsApiFlowTest
   PASS  Tests\Feature\WebAuthTest

  Tests:    103 passed (838 assertions)
  Duration: 10.91s
```

---

## 💻 Panduan Instalasi Lokal (Setup Guide)

### Prasyarat
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`)
- Composer >= 2.x
- MySQL >= 8.0 atau MariaDB
- Node.js & NPM (opsional untuk asset building)

### Langkah Pemasangan
1. **Clone Repositori**:
   ```bash
   git clone https://github.com/Liyace/Project-SIMRS.git
   cd Project-SIMRS
   ```

2. **Pasang Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env` dan sesuaikan koneksi database Anda:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   *Contoh konfigurasi database pada `.env`:*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=simrs_delta
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migrasi & Seeder Master**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---

## 🔑 Akun Demo Pengujian (Default Credentials)

*Seluruh akun di bawah menggunakan password default:* `12345`

| Role Pegawai | NIK / Username | Nama Akun | Penempatan / Spesialisasi |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `6301010001` | Super Admin RS | IT & SIMRS Security |
| **Dokter Umum** | `6301010010` | dr. Andi Pratama | Poli Umum |
| **Dokter Gigi** | `6301010011` | drg. Ratna Sari, Sp.KG | Poli Gigi |
| **Dokter Anak** | `6301010012` | dr. Sarah Nur, Sp.A | Poli Anak |
| **Dokter Penyakit Dalam**| `6301010002` | dr. Budi Santoso, Sp.PD | Poli Penyakit Dalam |
| **Dokter Mata** | `6301010013` | dr. Farhan, Sp.M | Poli Mata |
| **Perawat Skrining** | `6301010003` | Ns. Siti Nurhaliza, S.Kep | Instalasi Rawat Jalan |
| **Kasir Utama** | `6301010004` | Rahmat Kasir Utama | Billing & Keuangan |
| **Kasir Staf** | `6301010007` | Kasir Staf Delta | Billing Loket |
| **Apoteker** | `6301010005` | Apt. Dian Permata, S.Farm | Instalasi Farmasi |
| **Petugas Loket** | `6301010006` | Indah Permatasari, A.Md.Kes | Admisi Pasien |
| **Resepsionis** | `6301010008` | Rina Astuti, S.I.Kom | Informasi & Display TV |

*Catatan: Pasien dapat membuat akun baru mandiri secara langsung melalui halaman registrasi publik atau aplikasi mobile.*

---

## 📄 Lisensi & Kontribusi

Proyek ini dikembangkan sebagai portofolio implementasi arsitektur sistem informasi enterprise (*Hospital Management Information System*). Lisensi terbuka di bawah [MIT License](LICENSE).

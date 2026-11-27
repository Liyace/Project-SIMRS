<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Poli;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Master Poliklinik Tersedia Terlebih Dahulu
        $polis = [
            ['kode_poli' => 'UMU', 'nama_poli' => 'Poli Umum', 'biaya_konsultasi' => 50000],
            ['kode_poli' => 'GIG', 'nama_poli' => 'Poli Gigi', 'biaya_konsultasi' => 75000],
            ['kode_poli' => 'ANK', 'nama_poli' => 'Poli Anak', 'biaya_konsultasi' => 60000],
            ['kode_poli' => 'INT', 'nama_poli' => 'Poli Penyakit Dalam', 'biaya_konsultasi' => 90000],
            ['kode_poli' => 'MAT', 'nama_poli' => 'Poli Mata', 'biaya_konsultasi' => 80000],
        ];
        foreach ($polis as $p) {
            Poli::updateOrCreate(['kode_poli' => $p['kode_poli']], $p);
        }

        $poliUmu = Poli::where('kode_poli', 'UMU')->first();
        $poliGig = Poli::where('kode_poli', 'GIG')->first();
        $poliAnk = Poli::where('kode_poli', 'ANK')->first();
        $poliInt = Poli::where('kode_poli', 'INT')->first();
        $poliMat = Poli::where('kode_poli', 'MAT')->first();

        $accounts = [
            [
                'nik'           => '6301010001',
                'nip'           => '19800101 200501 1 001',
                'nama'          => 'Super Admin RS',
                'password_hash' => Hash::make('12345'),
                'role'          => 'admin',
                'no_hp'         => '081234567801',
                'is_admin'      => true,
            ],
            // ── DOKTER SPESIALIS PER POLIKLINIK ──
            [
                'nik'           => '6301010010',
                'nip'           => '19870415 201201 1 003',
                'nama'          => 'dr. Andi Pratama',
                'password_hash' => Hash::make('12345'),
                'role'          => 'dokter',
                'id_poli'       => $poliUmu?->id_poli,
                'spesialisasi'  => 'Dokter Umum & Gawat Darurat',
                'no_hp'         => '081234567820',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010011',
                'nip'           => '19890620 201402 2 004',
                'nama'          => 'drg. Ratna Sari, Sp.KG',
                'password_hash' => Hash::make('12345'),
                'role'          => 'dokter',
                'id_poli'       => $poliGig?->id_poli,
                'spesialisasi'  => 'Spesialis Konservasi Gigi',
                'no_hp'         => '081234567821',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010012',
                'nip'           => '19910912 201603 2 005',
                'nama'          => 'dr. Sarah Nur, Sp.A',
                'password_hash' => Hash::make('12345'),
                'role'          => 'dokter',
                'id_poli'       => $poliAnk?->id_poli,
                'spesialisasi'  => 'Spesialis Kesehatan Anak',
                'no_hp'         => '081234567822',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010002',
                'nip'           => '19850312 201001 1 002',
                'nama'          => 'dr. Budi Santoso, Sp.PD',
                'password_hash' => Hash::make('12345'),
                'role'          => 'dokter',
                'id_poli'       => $poliInt?->id_poli,
                'spesialisasi'  => 'Spesialis Penyakit Dalam',
                'no_hp'         => '081234567802',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010013',
                'nip'           => '19861125 201101 1 006',
                'nama'          => 'dr. Farhan, Sp.M',
                'password_hash' => Hash::make('12345'),
                'role'          => 'dokter',
                'id_poli'       => $poliMat?->id_poli,
                'spesialisasi'  => 'Spesialis Kesehatan Mata',
                'no_hp'         => '081234567823',
                'is_admin'      => false,
            ],
            // ── PERAWAT & TENAGA KESEHATAN LAINNYA ──
            [
                'nik'           => '6301010003',
                'nip'           => '19900820 201402 2 005',
                'nama'          => 'Ns. Siti Nurhaliza, S.Kep',
                'password_hash' => Hash::make('12345'),
                'role'          => 'perawat',
                'no_hp'         => '081234567803',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010004',
                'nip'           => '19920415 201801 1 003',
                'nama'          => 'Rahmat Kasir Utama',
                'password_hash' => Hash::make('12345'),
                'role'          => 'admin_kasir',
                'no_hp'         => '081234567804',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010005',
                'nip'           => '19931105 201903 2 004',
                'nama'          => 'Apt. Dian Permata, S.Farm',
                'password_hash' => Hash::make('12345'),
                'role'          => 'apoteker',
                'no_hp'         => '081234567805',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010006',
                'nip'           => '19950510 202101 2 006',
                'nama'          => 'Indah Permatasari, A.Md.Kes',
                'password_hash' => Hash::make('12345'),
                'role'          => 'loket',
                'no_hp'         => '081234567806',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010007',
                'nip'           => null,
                'nama'          => 'Kasir Staf Delta',
                'password_hash' => Hash::make('12345'),
                'role'          => 'kasir',
                'no_hp'         => '081234567807',
                'is_admin'      => false,
            ],
            [
                'nik'           => '6301010008',
                'nip'           => '19960714 202201 2 007',
                'nama'          => 'Rina Astuti, S.I.Kom',
                'password_hash' => Hash::make('12345'),
                'role'          => 'resepsionis',
                'no_hp'         => '081234567810',
                'is_admin'      => false,
            ],
        ];

        foreach ($accounts as $acc) {
            Pegawai::updateOrCreate(['nik' => $acc['nik']], $acc);
        }
    }
}

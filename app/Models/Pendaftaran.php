<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';
    protected $primaryKey = 'id_pendaftaran';

    protected $fillable = [
        'no_pendaftaran',
        'id_pasien',
        'id_poli',
        'id_dokter',
        'no_antrian',
        'tanggal_daftar',
        'cara_bayar',
        'status_antrian',
        'keluhan_awal',
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'id_pasien', 'id_pasien');
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'id_poli', 'id_poli');
    }

    public function dokter()
    {
        return $this->belongsTo(Pegawai::class, 'id_dokter', 'id_pegawai');
    }

    public function screening()
    {
        return $this->hasOne(ScreeningAwal::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function screeningAwal()
    {
        return $this->hasOne(ScreeningAwal::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function rekamMedis()
    {
        return $this->hasOne(RekamMedis::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function resep()
    {
        return $this->hasOne(Resep::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'id_pendaftaran', 'id_pendaftaran');
    }
}

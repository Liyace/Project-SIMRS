<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningAwal extends Model
{
    protected $table = 'screening_awal';
    protected $primaryKey = 'id_screening';

    protected $fillable = [
        'id_pendaftaran',
        'id_perawat',
        'sistol',
        'diastol',
        'suhu_tubuh',
        'berat_badan',
        'tinggi_badan',
        'denyut_nadi',
        'catatan_perawat',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function perawat()
    {
        return $this->belongsTo(Pegawai::class, 'id_perawat', 'id_pegawai');
    }
}

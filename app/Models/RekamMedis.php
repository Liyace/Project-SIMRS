<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    protected $table = 'rekam_medis';
    protected $primaryKey = 'id_rekam_medis';

    protected $fillable = [
        'id_pendaftaran',
        'id_dokter',
        'anamnesis',
        'diagnosa_utama',
        'kode_icd10',
        'diagnosa_sekunder',
        'terapi_tindakan',
        'catatan_edukasi',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function dokter()
    {
        return $this->belongsTo(Pegawai::class, 'id_dokter', 'id_pegawai');
    }

    public function tindakanMedis()
    {
        return $this->hasMany(TindakanMedis::class, 'id_rekam_medis', 'id_rekam_medis');
    }
}

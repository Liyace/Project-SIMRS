<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $table = 'resep';
    protected $primaryKey = 'id_resep';

    protected $fillable = [
        'no_resep',
        'id_pendaftaran',
        'id_dokter',
        'id_apoteker',
        'total_harga_obat',
        'status_resep',
        'catatan_dokter',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function dokter()
    {
        return $this->belongsTo(Pegawai::class, 'id_dokter', 'id_pegawai');
    }

    public function apoteker()
    {
        return $this->belongsTo(Pegawai::class, 'id_apoteker', 'id_pegawai');
    }

    public function details()
    {
        return $this->hasMany(ResepDetail::class, 'id_resep', 'id_resep');
    }
}

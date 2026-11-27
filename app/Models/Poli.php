<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $table = 'poli';
    protected $primaryKey = 'id_poli';

    protected $fillable = [
        'kode_poli',
        'nama_poli',
        'biaya_konsultasi',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'id_poli', 'id_poli');
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class, 'id_poli', 'id_poli');
    }

    public function dokters()
    {
        return $this->hasMany(Pegawai::class, 'id_poli', 'id_poli')->where('role', 'dokter');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakanMedis extends Model
{
    protected $table = 'tindakan_medis';
    protected $primaryKey = 'id_tindakan_medis';

    protected $fillable = [
        'id_rekam_medis',
        'id_layanan',
        'qty',
        'tarif_saat_ini',
        'subtotal',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'id_rekam_medis', 'id_rekam_medis');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }
}

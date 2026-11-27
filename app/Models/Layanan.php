<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    public $timestamps = false;

    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';

    protected $fillable = [
        'nama_layanan',
        'harga_layanan',
    ];

    public function tindakanMedis()
    {
        return $this->hasMany(TindakanMedis::class, 'id_layanan', 'id_layanan');
    }
}

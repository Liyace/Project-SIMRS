<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';
    protected $primaryKey = 'id_obat';

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'satuan',
        'harga_beli',
        'harga_obat',
        'stok',
        'stok_minimum',
    ];

    public function resepDetail()
    {
        return $this->hasMany(ResepDetail::class, 'id_obat', 'id_obat');
    }
}

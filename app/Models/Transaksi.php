<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'no_invoice',
        'id_pendaftaran',
        'id_resep',
        'id_layanan',
        'id_pegawai',
        'total_layanan',
        'total_bayar',
        'total_resep',
        'jasa_perawat',
        'jumlah_dibayar',
        'kembalian',
        'metode_bayar',
        'status_pembayaran',
        'waktu_pembayaran',
    ];

    protected $casts = [
        'waktu_pembayaran' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'id_pendaftaran', 'id_pendaftaran');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep', 'id_resep');
    }

    public function kasir()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'id_transaksi', 'id_transaksi');
    }
}

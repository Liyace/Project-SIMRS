<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanReset extends Model
{
    use HasFactory;

    protected $table = 'permintaan_reset';
    protected $primaryKey = 'id_permintaan';

    protected $fillable = [
        'tipe_pengguna',
        'id_pengguna',
        'nik',
        'nama',
        'no_hp',
        'tipe_permintaan',
        'catatan',
        'status',
        'password_baru_plain',
        'catatan_admin',
        'diproses_oleh',
        'diproses_pada',
    ];

    protected $casts = [
        'diproses_pada' => 'datetime',
    ];

    public function adminPemroses()
    {
        return $this->belongsTo(Pegawai::class, 'diproses_oleh', 'id_pegawai');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pengguna', 'id_pegawai');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'id_pengguna', 'id_pasien');
    }

    /**
     * Helper untuk label tipe permintaan
     */
    public function getLabelTipePermintaanAttribute(): string
    {
        return match ($this->tipe_permintaan) {
            'password' => 'Lupa Password',
            '2fa'      => 'Reset 2FA',
            'keduanya' => 'Password & 2FA',
            default    => 'Reset Akun',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pegawai extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';

    protected $fillable = [
        'nik',
        'nip',
        'nama',
        'jenis_kelamin',
        'password_hash',
        'role',
        'id_poli',
        'spesialisasi',
        'foto_url',
        'no_hp',
        'is_admin',
        'two_factor_enabled',
        'two_factor_secret',
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'id_poli', 'id_poli');
    }

    public function getFotoUrlAttribute($value): string
    {
        if ($value) {
            $pathOnly = parse_url($value, PHP_URL_PATH) ?? $value;
            $cleanPath = ltrim($pathOnly, '/');
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }
        }

        return ($this->jenis_kelamin === 'P')
            ? asset('images/avatar_female.jpg')
            : asset('images/avatar_male.jpg');
    }

    protected $casts = [
        'is_admin'           => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Cek apakah staf ini bertindak sebagai Kasir Utama (Supervisor / Head Cashier)
     */
    public function isKasirUtama(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['admin_kasir', 'admin kasir', 'kasir utama', 'kasir_utama'])
            || $this->nik === '6301010004'
            || str_contains(strtolower($this->nama ?? ''), 'utama');
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function pendaftaranDokter()
    {
        return $this->hasMany(Pendaftaran::class, 'id_dokter', 'id_pegawai');
    }

    public function screeningPerawat()
    {
        return $this->hasMany(ScreeningAwal::class, 'id_perawat', 'id_pegawai');
    }

    public function rekamMedisDokter()
    {
        return $this->hasMany(RekamMedis::class, 'id_dokter', 'id_pegawai');
    }

    public function resepDokter()
    {
        return $this->hasMany(Resep::class, 'id_dokter', 'id_pegawai');
    }

    public function transaksiKasir()
    {
        return $this->hasMany(Transaksi::class, 'id_pegawai', 'id_pegawai');
    }
}

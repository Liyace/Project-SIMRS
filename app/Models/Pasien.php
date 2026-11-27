<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pasien extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pasien';
    protected $primaryKey = 'id_pasien';

    protected $fillable = [
        'rm',
        'nik',
        'username',
        'nama_pasien',
        'password_hash',
        'jenis_kelamin',
        'tanggal_lahir',
        'no_hp',
        'alamat',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getFotoUrlAttribute(): string
    {
        return ($this->jenis_kelamin === 'P')
            ? asset('images/avatar_female.jpg')
            : asset('images/avatar_male.jpg');
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'id_pasien', 'id_pasien');
    }
}

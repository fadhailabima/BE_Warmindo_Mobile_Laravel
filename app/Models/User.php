<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'password',
    ];

    protected $primaryKey = 'idkaryawan'; // Memberitahu Eloquent bahwa primary key adalah 'id' (bukan 'id' integer default)
    public $incrementing = false; // Menyatakan bahwa 'id' bukanlah auto-incrementing
    protected $keyType = 'string';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function AktivitasPengguna()
    {
        return $this->hasMany(AktivitasPengguna::class, 'id_pengguna');
    }

    // Relasi ke tabel 'DosenWali'
    public function Transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_pengguna');
    }
}

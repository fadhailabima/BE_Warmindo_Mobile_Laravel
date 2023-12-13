<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivitasPengguna extends Model
{
    use HasFactory;

    protected $table = 'aktivitaspenggunas';

    protected $fillable = [
        'tanggal',
        'waktu',
        'id_pengguna',
        'aktivitas'
    ];

    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'idkaryawan');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';

    protected $fillable = [
        'namapelanggan',
        'tanggaldaftar',
        'waktudaftar',
        'poin',
        'status',
    ];

    public function Transaksi()
    {
        return $this->hasMany(Transaksi::class, 'idpelanggan');
    }

    public function PoinTransaksi()
    {
        return $this->hasOne(PoinTransaksi::class, 'idpelanggan');
    }
}

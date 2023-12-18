<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';

    protected $primaryKey = 'idtransaksi'; // Memberitahu Eloquent bahwa primary key adalah 'id' (bukan 'id' integer default)
    public $incrementing = false; // Menyatakan bahwa 'id' bukanlah auto-incrementing
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'waktu',
        'shift',
        'id_pengguna',
        'id_pelanggan',
        'status',
        'kode_meja',
        'namapelanggan',
        'total',
        'metode_pembayaran',
        'totaldiskon',
        'idpromosi'
    ];

    protected $guarded = [];

    public function Pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'idkaryawan');
    }
    public function Promosi()
    {
        return $this->belongsTo(Promosi::class, 'id_promosi', 'id');
    }
    public function DetailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'idtransaksi');
    }

}

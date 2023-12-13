<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoinTransaksi extends Model
{
    use HasFactory;

    protected $table = 'pointransaksis';

    protected $fillable = [
        'tanggal',
        'waktu',
        'idpelanggan',
        'jumlahpoin',
        'status',
        'poinawal',
        'poinakhir',
        'sumber'
    ];

    protected $guarded = [];

    public function Pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'idpelanggan', 'id');
    }
}

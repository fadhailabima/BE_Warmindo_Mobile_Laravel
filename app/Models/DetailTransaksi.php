<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;
    protected $table = 'detailtransaksis';

    protected $fillable = [
        'idtransaksi',
        'idmenu',
        'namamenu',
        'harga',
        'jumlah',
        'subtotal',
        'status',
    ];

    protected $guarded = [];

    public function Transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'idtransaksi', 'idtransaksi');
    }
    public function Menu()
    {
        return $this->belongsTo(Menu::class, 'idmenu', 'id');
    }
}

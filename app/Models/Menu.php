<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'namamenu',
        'kategori',
        'harga',
        'gambar'
    ];

    protected $guarded = [];

    public function DetailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'idmenu', 'id');
    }
}

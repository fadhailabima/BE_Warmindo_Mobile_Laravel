<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warung extends Model
{
    use HasFactory;

    protected $table = 'warungs';
    protected $primaryKey = 'idwarung'; // Memberitahu Eloquent bahwa primary key adalah 'id' (bukan 'id' integer default)
    public $incrementing = false; // Menyatakan bahwa 'id' bukanlah auto-incrementing
    protected $keyType = 'string';

    protected $fillable = [
        'namamenu',
        'kategori',
        'harga',
        'gambar'
    ];

    public function Meja()
    {
        return $this->hasMany(Meja::class, 'id_warung');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    use HasFactory;

    protected $table = 'mejas';

    protected $fillable = [
        'kodemeja',
        'id_warung',
    ];

    protected $guarded = [];

    public function Warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['produk_id', 'id', 'jumlah', 'tanggal_kadaluarsa'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

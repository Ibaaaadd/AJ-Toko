<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'produk_id',
        'jumlah',
        'tanggal_kadaluarsa',
        'tanggal_pembelian'
    ];

    protected $dates = ['tanggal_kadaluarsa', 'tanggal_pembelian'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

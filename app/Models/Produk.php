<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = ['kode', 'nama', 'harga', 'rop', 'total_stok', 'lead_time'];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }
}

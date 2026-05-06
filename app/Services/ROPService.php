<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\Penjualan;
use Carbon\Carbon;

class ROPService
{
    /**
     * Hitung ROP untuk produk tertentu
     */
    public function hitungROP($produkId)
    {
        $produk = Produk::findOrFail($produkId);

        // Hitung rata-rata penjualan harian berdasarkan data 30 hari terakhir
        $rataRataPenjualanHarian = $this->hitungRataRataPenjualanHarian($produkId);

        // ROP = Lead Time × Rata-rata permintaan
        $rop = $rataRataPenjualanHarian * $produk->lead_time;

        // Update data produk
        $produk->update([
            'rata_rata_penjualan_harian' => $rataRataPenjualanHarian,
            'rop' => ceil($rop) // Dibulatkan ke atas
        ]);

        return $rop;
    }

    /**
     * Hitung ROP untuk semua produk
     */
    public function hitungSemuaROP()
    {
        $produks = Produk::all();

        foreach ($produks as $produk) {
            $this->hitungROP($produk->id);
        }
    }

    /**
     * Hitung rata-rata penjualan harian berdasarkan data 30 hari terakhir
     */
    private function hitungRataRataPenjualanHarian($produkId)
    {
        $tanggalMulai = Carbon::now()->subDays(30);
        $tanggalSekarang = Carbon::now();

        // Ambil total penjualan 30 hari terakhir
        $totalPenjualan = Penjualan::where('produk_id', $produkId)
            ->where('tanggal_penjualan', '>=', $tanggalMulai)
            ->where('tanggal_penjualan', '<=', $tanggalSekarang)
            ->sum('jumlah');

        // Hitung jumlah hari yang memiliki data penjualan
        $jumlahHariDenganPenjualan = Penjualan::where('produk_id', $produkId)
            ->where('tanggal_penjualan', '>=', $tanggalMulai)
            ->where('tanggal_penjualan', '<=', $tanggalSekarang)
            ->selectRaw('COUNT(DISTINCT DATE(tanggal_penjualan)) as hari')
            ->value('hari');

        // Jika tidak ada penjualan dalam 30 hari terakhir, return 0
        if ($jumlahHariDenganPenjualan == 0) {
            return 0;
        }

        // Rata-rata penjualan per hari
        return $totalPenjualan / $jumlahHariDenganPenjualan;
    }

    /**
     * Cek produk yang perlu di-reorder
     */
    public function cekProdukPerluReorder()
    {
        return Produk::whereRaw('total_stok <= rop')
            ->where('rop', '>', 0)
            ->get();
    }
}

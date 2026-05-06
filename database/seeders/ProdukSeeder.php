<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        DB::table('produks')->delete();
        DB::table('batches')->delete();
        DB::table('pembelians')->delete();
        DB::table('penjualans')->delete();

        $produks = [
            ['kode' => 'PRD001', 'nama' => 'Sabun Mandi', 'harga' => 5000],
            ['kode' => 'PRD002', 'nama' => 'Shampo Botol', 'harga' => 15000],
            ['kode' => 'PRD003', 'nama' => 'Pasta Gigi', 'harga' => 7000],
        ];

        foreach ($produks as $produkData) {
            $produk = \App\Models\Produk::create([
                'kode' => $produkData['kode'],
                'nama' => $produkData['nama'],
                'harga' => $produkData['harga'],
                'total_stok' => 100, // asumsikan stok awal
                'rop' => 10,
                'lead_time' => 5,
                'rata_rata_penjualan_harian' => 0,
            ]);

            // Tambahkan pembelian (batch)
            for ($i = 0; $i < 3; $i++) {
                $tanggalPembelian = Carbon::now()->subDays(30 - ($i * 10));
                $jumlah = rand(20, 40);
                $kadaluarsa = Carbon::now()->addDays(rand(30, 90));

                \App\Models\Pembelian::create([
                    'produk_id' => $produk->id,
                    'kode' => 'PB' . strtoupper(uniqid()),
                    'jumlah' => $jumlah,
                    'tanggal_kadaluarsa' => $kadaluarsa,
                    'tanggal_pembelian' => $tanggalPembelian,
                ]);

                \App\Models\Batch::create([
                    'produk_id' => $produk->id,
                    'jumlah' => $jumlah,
                    'tanggal_kadaluarsa' => $kadaluarsa,
                ]);
            }

            // Tambahkan penjualan 30 hari terakhir
            for ($d = 30; $d >= 1; $d--) {
                $tanggal = Carbon::now()->subDays($d);
                $jumlahJual = rand(0, 5); // bisa 0 agar realistis

                if ($jumlahJual > 0) {
                    \App\Models\Penjualan::create([
                        'produk_id' => $produk->id,
                        'kode' => 'PJ' . strtoupper(uniqid()),
                        'jumlah' => $jumlahJual,
                        'tanggal_penjualan' => $tanggal,
                    ]);
                }
            }
        }
    }
}

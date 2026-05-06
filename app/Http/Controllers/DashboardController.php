<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Produk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $twoMonthsLater = $now->copy()->addMonths(2);

        // Ambil batch yang kadaluarsa dalam 2 bulan ke depan
        $batchs = Batch::whereBetween('tanggal_kadaluarsa', [$now->toDateString(), $twoMonthsLater->toDateString()])
            ->with('produk')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        // Ambil produk yang perlu direstok
        $produks = Produk::whereColumn('total_stok', '<=', 'rop')->get();

        // Flash notifikasi jika setelah login
        if (session('after_login') && $produks->count() > 0) {
            $produkNames = $produks->pluck('nama')->take(3)->implode(', ');
            $totalRestok = $produks->count();

            $message = $totalRestok > 3
                ? "Ada {$totalRestok} produk yang perlu direstok: {$produkNames}, dan lain-lain."
                : "Ada {$totalRestok} produk yang perlu direstok: {$produkNames}.";

            session()->flash('restok_alert', $message);
            session()->forget('after_login');
        }

        // Hitung total data
        $totalProduk = Produk::count();
        $totalPembelian = Pembelian::count();
        $totalPenjualan = Penjualan::count();

        // Tahun aktif (dari request atau default tahun sekarang)
        $tahunDipilih = $request->input('tahun', $now->year);
        $driver = DB::getDriverName();

        // Ambil daftar tahun unik dari pembelian dan penjualan
        if ($driver === 'pgsql') {
            $tahunPembelian = DB::table('pembelians')
                ->select(DB::raw("EXTRACT(YEAR FROM tanggal_pembelian)::int as tahun"))
                ->distinct()
                ->pluck('tahun');

            $tahunPenjualan = DB::table('penjualans')
                ->select(DB::raw("EXTRACT(YEAR FROM tanggal_penjualan)::int as tahun"))
                ->distinct()
                ->pluck('tahun');
        } else {
            $tahunPembelian = DB::table('pembelians')
                ->select(DB::raw("YEAR(tanggal_pembelian) as tahun"))
                ->distinct()
                ->pluck('tahun');

            $tahunPenjualan = DB::table('penjualans')
                ->select(DB::raw("YEAR(tanggal_penjualan) as tahun"))
                ->distinct()
                ->pluck('tahun');
        }

        $tahunList = $tahunPembelian->merge($tahunPenjualan)->unique()->sortDesc()->values();

        // Format per bulan
        $formatBulanPembelian = $driver === 'pgsql'
            ? "TO_CHAR(tanggal_pembelian, 'YYYY-MM')"
            : "DATE_FORMAT(tanggal_pembelian, '%Y-%m')";

        $formatBulanPenjualan = $driver === 'pgsql'
            ? "TO_CHAR(tanggal_penjualan, 'YYYY-MM')"
            : "DATE_FORMAT(tanggal_penjualan, '%Y-%m')";

        // Data pembelian per bulan
        $pembelianPerBulan = DB::table('pembelians')
            ->select(DB::raw("$formatBulanPembelian as bulan"), DB::raw("SUM(jumlah) as total"))
            ->when($driver === 'pgsql', function ($query) use ($tahunDipilih) {
                $query->whereRaw("EXTRACT(YEAR FROM tanggal_pembelian)::int = ?", [$tahunDipilih]);
            }, function ($query) use ($tahunDipilih) {
                $query->whereYear('tanggal_pembelian', $tahunDipilih);
            })
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // Data penjualan per bulan
        $penjualanPerBulan = DB::table('penjualans')
            ->select(DB::raw("$formatBulanPenjualan as bulan"), DB::raw("SUM(jumlah) as total"))
            ->when($driver === 'pgsql', function ($query) use ($tahunDipilih) {
                $query->whereRaw("EXTRACT(YEAR FROM tanggal_penjualan)::int = ?", [$tahunDipilih]);
            }, function ($query) use ($tahunDipilih) {
                $query->whereYear('tanggal_penjualan', $tahunDipilih);
            })
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // Gabungkan bulan dari pembelian & penjualan
        $bulanLabels = collect($pembelianPerBulan->keys())
            ->merge($penjualanPerBulan->keys())
            ->unique()
            ->sort()
            ->values();

        return view('dashboard', compact(
            'produks',
            'batchs',
            'totalProduk',
            'totalPembelian',
            'totalPenjualan',
            'pembelianPerBulan',
            'penjualanPerBulan',
            'bulanLabels',
            'tahunList',
            'tahunDipilih'
        ));
    }
}

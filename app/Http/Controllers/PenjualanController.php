<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\PenjualanExport;
use Maatwebsite\Excel\Facades\Excel;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::all();
        $penjualans = Penjualan::with('produk')->get(); // Added with() to load produk relationship
        return view('penjualan.index', compact('penjualans', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_penjualan' => 'required|date',
        ]);

        $produk = Produk::findOrFail($request->produk_id);

        // Check if stock is sufficient
        if ($request->jumlah > $produk->total_stok) {
            return back()->withErrors(['jumlah' => 'Stok tidak mencukupi untuk penjualan.']);
        }

        // FEFO: Take batches from the earliest expiry date
        $sisa = $request->jumlah;
        $batches = Batch::where('produk_id', $request->produk_id)
            ->where('jumlah', '>', 0)
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        foreach ($batches as $batch) {
            if ($sisa <= 0) break;

            $ambil = min($batch->jumlah, $sisa);
            $batch->jumlah -= $ambil;
            $batch->save();

            $sisa -= $ambil;
        }

        // Generate random code
        $kode = strtoupper(Str::random(8));

        // Save to penjualan table
        Penjualan::create([
            'kode' => $kode,
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_penjualan' => $request->tanggal_penjualan,
        ]);

        // Update total_stok in produk
        $produk->decrement('total_stok', $request->jumlah);

        // Update ROP dan rata-rata penjualan harian (berdasarkan 7 hari)
        $this->updateROP($produk);

        return redirect()->back()->with('success', 'Penjualan berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_penjualan' => 'required|date',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $produk = Produk::findOrFail($request->produk_id);

        // 1. Kembalikan jumlah batch yang sebelumnya dikeluarkan
        $sisaRestore = $penjualan->jumlah;
        $batchesRestore = Batch::where('produk_id', $produk->id)
            ->orderBy('tanggal_kadaluarsa', 'asc') // ✔ urut dari yang paling dekat kadaluarsanya
            ->get();

        foreach ($batchesRestore as $batch) {
            if ($sisaRestore <= 0) break;

            $batch->jumlah += $sisaRestore;
            $batch->save();
            $sisaRestore = 0; // semua dikembalikan sekaligus
        }

        // 2. Kurangi batch sesuai FEFO dengan jumlah baru
        $sisaBaru = $request->jumlah;
        $batches = Batch::where('produk_id', $produk->id)
            ->where('jumlah', '>', 0)
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->get();

        foreach ($batches as $batch) {
            if ($sisaBaru <= 0) break;

            $ambil = min($batch->jumlah, $sisaBaru);
            $batch->jumlah -= $ambil;
            $batch->save();

            $sisaBaru -= $ambil;
        }

        // 3. Update data penjualan
        $penjualan->update([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_penjualan' => $request->tanggal_penjualan,
        ]);

        // 4. Update total_stok di produk
        $produk->total_stok = Batch::where('produk_id', $produk->id)->sum('jumlah');
        $produk->save();

        // 5. Update ROP dan rata-rata harian
        $this->updateROP($produk);

        return redirect()->back()->with('success', 'Penjualan berhasil diperbarui.');
    }

    private function updateROP(Produk $produk)
    {
        $mingguTerakhir = now()->subDays(7);

        $penjualan = Penjualan::where('produk_id', $produk->id)
            ->where('tanggal_penjualan', '>=', $mingguTerakhir)
            ->get();

        $totalPenjualan = $penjualan->sum('jumlah');
        $jumlahHariUnik = $penjualan->pluck('tanggal_penjualan')->unique()->count();

        if ($jumlahHariUnik === 0) {
            $rata2 = 0;
        } else {
            $rata2 = $totalPenjualan / $jumlahHariUnik;
        }

        $produk->rata_rata_penjualan_harian = round($rata2, 2);
        $produk->rop = ceil($produk->lead_time * $rata2);
        $produk->save();
    }

    public function export()
    {
        return Excel::download(new PenjualanExport, 'data_penjualan.xlsx');
    }
}

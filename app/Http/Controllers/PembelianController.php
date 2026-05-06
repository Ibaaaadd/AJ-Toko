<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Pembelian;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\PembelianExport;
use Maatwebsite\Excel\Facades\Excel;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::all();
        $pembelians = Pembelian::with('produk')->latest()->get(); // Eloquent, bukan DB::table

        // Tambahkan properti dinamis
        foreach ($pembelians as $pembelian) {
            $batch = Batch::where('produk_id', $pembelian->produk_id)
                ->where('tanggal_kadaluarsa', $pembelian->tanggal_kadaluarsa)
                ->where('jumlah', $pembelian->jumlah)
                ->first();

            $pembelian->setAttribute('boleh_edit_jumlah', $batch ? true : false);
        }

        return view('pembelian.index', compact('pembelians', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_kadaluarsa' => 'required|date|after_or_equal:today',
            'tanggal_pembelian' => 'required|date',
        ]);

        // Buat kode random
        $kode = strtoupper(Str::random(8));

        // Simpan ke tabel pembelian
        $pembelian = Pembelian::create([
            'kode' => $kode,
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
            'tanggal_pembelian' => $request->tanggal_pembelian,
        ]);

        // Simpan ke tabel batches
        Batch::create([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
        ]);

        // Update total stok produk
        Produk::where('id', $request->produk_id)->increment('total_stok', $request->jumlah);

        return redirect()->back()->with('success', "Pembelian berhasil disimpan. Kode: {$kode}");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_kadaluarsa' => 'required|date|after_or_equal:today',
            'tanggal_pembelian' => 'required|date',
        ]);

        $pembelian = Pembelian::findOrFail($id);
        $oldJumlah = $pembelian->jumlah;
        $oldProdukId = $pembelian->produk_id;

        // Update data pembelian
        $pembelian->update([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
            'tanggal_pembelian' => $request->tanggal_pembelian,
        ]);

        // Update batch sesuai pembelian
        $batch = Batch::where('produk_id', $oldProdukId)
            ->where('tanggal_kadaluarsa', $pembelian->tanggal_kadaluarsa)
            ->first();

        if ($batch) {
            // Update jumlah batch jika produk atau jumlah berubah
            $batch->update([
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
            ]);
        }

        // Update stok produk
        if ($oldProdukId != $request->produk_id) {
            // Kurangi stok produk lama, tambahkan ke produk baru
            Produk::where('id', $oldProdukId)->decrement('total_stok', $oldJumlah);
            Produk::where('id', $request->produk_id)->increment('total_stok', $request->jumlah);
        } else {
            // Hitung selisih jumlah
            $selisih = $request->jumlah - $oldJumlah;
            Produk::where('id', $request->produk_id)->increment('total_stok', $selisih);
        }

        return redirect()->back()->with('success', 'Data pembelian berhasil diperbarui!');
    }

    public function export()
    {
        return Excel::download(new PembelianExport, 'data_pembelian.xlsx');
    }
}

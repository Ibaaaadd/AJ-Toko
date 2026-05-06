<?php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PembelianExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pembelian::with('produk')->get()->map(function ($p) {
            return [
                'kode' => $p->kode,
                'produk' => $p->produk->nama,
                'jumlah' => $p->jumlah,
                'tanggal_kadaluarsa' => $p->tanggal_kadaluarsa,
                'tanggal_pembelian' => $p->tanggal_pembelian,
            ];
        });
    }

    public function headings(): array
    {
        return ['Kode', 'Produk', 'Jumlah', 'Tanggal Kadaluarsa', 'Tanggal Pembelian'];
    }
}

<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenjualanExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Penjualan::with('produk')->get()->map(function ($item) {
            return [
                'Kode Penjualan' => $item->kode,
                'Nama Produk' => $item->produk->nama ?? '-',
                'Jumlah' => $item->jumlah,
                'Tanggal Penjualan' => $item->tanggal_penjualan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kode Penjualan',
            'Nama Produk',
            'Jumlah',
            'Tanggal Penjualan',
        ];
    }
}

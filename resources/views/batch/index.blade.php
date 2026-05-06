@extends('layout.app')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Batch</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('batch.index') }}">Batch</a></li>
                            <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <p class="text-subtitle text-muted">Riwayat Stok Penjualan dan Pembelian</p>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped" style="table-layout: fixed; width: 100%;" id="table1">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Kadaluwarsa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batchs as $batch)
                                <tr>
                                    <td>batch {{ $batch->id }}</td>
                                    <td>{{ $batch->produk->nama }}</td>
                                    <td class="text-center">{{ $batch->jumlah }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->translatedFormat('d F Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal Tambah -->
        </section>
    </div>
@endsection

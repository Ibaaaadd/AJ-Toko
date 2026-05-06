@extends('layout.app')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pembelian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li>
                            <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <p class="text-subtitle text-muted">Kelola data penjualan produk</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first d-flex justify-content-end gap-2 mb-3">
                    @if (Auth::user()->role === 'admin')
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            Tambah Data
                        </button>
                    @endif
                    <a href="{{ route('pembelian.export') }}" class="btn btn-success">
                        Export Data
                    </a>
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
                                <th>Nama Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Kadaluwarsa</th>
                                @if (Auth::User()->role === 'admin')
                                    <th class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelians as $pembelian)
                                <tr>
                                    <td>{{ $pembelian->kode }}</td>
                                    <td>{{ $pembelian->produk->nama }}</td>
                                    <td class="text-center">{{ $pembelian->jumlah }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($pembelian->tanggal_kadaluarsa)->translatedFormat('d F Y') }}
                                    </td>
                                    @if (Auth::User()->role === 'admin')
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $pembelian->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal Tambah -->
            <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <form action="{{ route('pembelian.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTambahLabel">Tambah Pembelian</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="produkTambah">Produk</label>
                                    <select class="form-select" id="produkTambah" name="produk_id" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="jumlahTambah">Jumlah</label>
                                    <input type="number" class="form-control" id="jumlahTambah" name="jumlah"
                                        placeholder="Jumlah Barang" min="1" required>
                                </div>

                                <div class="mb-3">
                                    <label for="kadaluarsaTambah">Tanggal Kadaluarsa</label>
                                    <input type="date" class="form-control" id="kadaluarsaTambah"
                                        name="tanggal_kadaluarsa" required>
                                </div>

                                <div class="mb-3">
                                    <label for="pembelianTambah">Tanggal Pembelian</label>
                                    <input type="date" class="form-control" id="pembelianTambah" name="tanggal_pembelian"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-secondary"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @foreach ($pembelians as $pembelian)
                <div class="modal fade" id="modalEdit{{ $pembelian->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="modalEditLabel{{ $pembelian->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditLabel{{ $pembelian->id }}">Edit Pembelian</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <i data-feather="x"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Produk</label>
                                        <select class="form-select" name="produk_id" required>
                                            @foreach ($produks as $produk)
                                                <option value="{{ $produk->id }}"
                                                    {{ $produk->id == $pembelian->produk_id ? 'selected' : '' }}>
                                                    {{ $produk->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>Jumlah</label>
                                        <input type="number" class="form-control" name="jumlah"
                                            value="{{ $pembelian->jumlah }}" required min="1"
                                            {{ $pembelian->boleh_edit_jumlah ? '' : 'readonly' }}>
                                    </div>

                                    <div class="mb-3">
                                        <label>Tanggal Kadaluarsa</label>
                                        <input type="date" class="form-control" name="tanggal_kadaluarsa"
                                            value="{{ $pembelian->tanggal_kadaluarsa }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Tanggal Pembelian</label>
                                        <input type="date" class="form-control" name="tanggal_pembelian"
                                            value="{{ $pembelian->tanggal_pembelian }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            <!-- Modal Edit -->
        </section>
    </div>
@endsection

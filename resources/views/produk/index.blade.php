@extends('layout.app')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Produk</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('produk.index') }}">Produk</a></li>
                            <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <p class="text-subtitle text-muted">Semua Produk Toko</p>
                </div>

                @if (Auth::user()->role === 'admin')
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <div style="margin-bottom: 1.5rem" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalTambah">
                                Tambah Data
                            </div>
                        </nav>
                    </div>
                @endif

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
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>ROP</th>
                                <th>Lead Time</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $produk)
                                <tr>
                                    <td>{{ $produk->kode }}</td>
                                    <td>{{ $produk->nama }}</td>
                                    <td>Rp.{{ $produk->harga }}</td>
                                    <td>{{ $produk->total_stok }}</td>
                                    <td>{{ $produk->rop }}</td>
                                    <td>{{ $produk->lead_time }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $produk->id }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>

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
                        <form action="{{ route('produk.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTambahLabel">Tambah Produk</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="namaTambah">Nama Produk</label>
                                    <input type="text" class="form-control" id="namaTambah" name="nama"
                                        placeholder="Nama Produk" required>
                                </div>
                                <div class="mb-3">
                                    <label for="hargaTambah">Harga</label>
                                    <input type="number" class="form-control" id="hargaTambah" name="harga"
                                        placeholder="Harga Produk" required>
                                </div>
                                <div class="mb-3">
                                    <label for="LeadTambah">Harga</label>
                                    <input type="number" class="form-control" id="LeadTambah" name="lead_time"
                                        placeholder="Lead Time" required>
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

            @foreach ($produks as $produk)
                <div class="modal fade" id="modalEdit{{ $produk->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <form action="{{ route('produk.update', $produk->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Produk</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <i data-feather="x"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nama{{ $produk->id }}">Nama</label>
                                        <input type="text" class="form-control" name="nama"
                                            id="nama{{ $produk->id }}" value="{{ $produk->nama }}"
                                            placeholder="Nama Produk" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="harga{{ $produk->id }}">Harga</label>
                                        <input type="number" class="form-control" name="harga"
                                            id="harga{{ $produk->id }}" value="{{ $produk->harga }}"
                                            placeholder="Harga Produk" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rata{{ $produk->id }}">Rata-rata Penjualan Harian</label>
                                        <input type="number" step="0.01" class="form-control"
                                            id="rata{{ $produk->id }}"
                                            value="{{ $produk->rata_rata_penjualan_harian }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rop{{ $produk->id }}">Reorder Point (ROP)</label>
                                        <input type="number" class="form-control" id="rop{{ $produk->id }}"
                                            value="{{ $produk->rop }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="leadtime{{ $produk->id }}">Lead Time (hari)</label>
                                        <input type="number" name="lead_time" class="form-control"
                                            id="leadtime{{ $produk->id }}" value="{{ $produk->lead_time }}" required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    </div>
@endsection

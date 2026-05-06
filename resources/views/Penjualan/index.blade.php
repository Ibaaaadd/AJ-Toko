@extends('layout.app')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Penjualan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('penjualan.index') }}">Penjualan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Datatable</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <p class="text-subtitle text-muted">Kelola data penjualan produk</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first d-flex justify-content-end gap-2 mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        Tambah Data
                    </button>
                    <a href="{{ route('penjualan.export') }}" class="btn btn-success">
                        Export Data
                    </a>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped" style="table-layout: fixed; width: 100%;" id="table1">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Tanggal Penjualan</th>
                                <th class="text-center">Aksi</th>   
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penjualans as $penjualan)
                                <tr>
                                    <td>{{ $penjualan->kode }}</td>
                                    <td>{{ $penjualan->produk->nama }}</td>
                                    <td>{{ $penjualan->jumlah }}</td>
                                    <td>{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $penjualan->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->role === 'admin' ? '5' : '4' }}" class="text-center">
                                        Tidak
                                        ada data penjualan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Modal Tambah --}}
            @if (Auth::user()->role === 'admin')
                <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <form action="{{ route('penjualan.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTambahLabel">Tambah Penjualan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="produkTambah" class="form-label">Produk</label>
                                        <select class="form-select" id="produkTambah" name="produk_id" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($produks as $produk)
                                                <option value="{{ $produk->id }}" data-stok="{{ $produk->total_stok }}">
                                                    {{ $produk->nama }} (Stok: {{ $produk->total_stok }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="jumlahTambah" class="form-label">Jumlah</label>
                                        <input type="number" class="form-control" id="jumlahTambah" name="jumlah"
                                            placeholder="Masukkan jumlah" min="1" required>
                                        <small class="form-text text-muted" id="stokInfo"></small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tanggal_penjualan" class="form-label">Tanggal Penjualan</label>
                                        <input type="date" class="form-control" id="tanggal_penjualan"
                                            name="tanggal_penjualan" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @foreach ($penjualans as $penjualan)
                <div class="modal fade" id="modalEdit{{ $penjualan->id }}" tabindex="-1"
                    aria-labelledby="modalEditLabel{{ $penjualan->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditLabel{{ $penjualan->id }}">Edit Penjualan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="produkEdit{{ $penjualan->id }}" class="form-label">Produk</label>
                                        <select class="form-select" id="produkEdit{{ $penjualan->id }}" name="produk_id"
                                            required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($produks as $produk)
                                                <option value="{{ $produk->id }}"
                                                    data-stok="{{ $produk->total_stok }}"
                                                    {{ $penjualan->produk_id == $produk->id ? 'selected' : '' }}>
                                                    {{ $produk->nama }} (Stok: {{ $produk->total_stok }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="jumlahEdit{{ $penjualan->id }}" class="form-label">Jumlah</label>
                                        <input type="number" class="form-control" id="jumlahEdit{{ $penjualan->id }}"
                                            name="jumlah" value="{{ $penjualan->jumlah }}" min="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tanggalEdit{{ $penjualan->id }}" class="form-label">Tanggal
                                            Penjualan</label>
                                        <input type="date" class="form-control" id="tanggalEdit{{ $penjualan->id }}"
                                            name="tanggal_penjualan" value="{{ $penjualan->tanggal_penjualan }}"
                                            required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Perbarui</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    </div>

    {{-- JavaScript for stock validation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produkSelect = document.getElementById('produkTambah');
            const jumlahInput = document.getElementById('jumlahTambah');
            const stokInfo = document.getElementById('stokInfo');

            if (produkSelect && jumlahInput && stokInfo) {
                produkSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const stok = selectedOption.getAttribute('data-stok');

                    if (stok) {
                        stokInfo.textContent = `Stok tersedia: ${stok}`;
                        jumlahInput.setAttribute('max', stok);
                    } else {
                        stokInfo.textContent = '';
                        jumlahInput.removeAttribute('max');
                    }

                    jumlahInput.value = '';
                });

                jumlahInput.addEventListener('input', function() {
                    const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                    const stok = parseInt(selectedOption.getAttribute('data-stok'));
                    const jumlah = parseInt(this.value);

                    if (jumlah > stok) {
                        this.setCustomValidity(`Jumlah tidak boleh melebihi stok (${stok})`);
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });
    </script>
@endsection

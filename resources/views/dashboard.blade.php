@extends('layout.app')

@section('content')
    <div class="page-heading">
        <h3>Dashboard</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-12">
                <div class="row">
                    <div class="col-6 col-lg-4 col-md-6">
                        <a href="{{ route('produk.index') }}" class="text-decoration-none text-dark">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon purple">
                                                <i class="iconly-boldShow"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Produk</h6>
                                            <h6 class="font-extrabold mb-0">{{ number_format($totalProduk) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Card Pembelian -->
                    <div class="col-6 col-lg-4 col-md-6">
                        <a href="{{ route('pembelian.index') }}" class="text-decoration-none text-dark">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon blue">
                                                <i class="iconly-boldProfile"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Pembelian</h6>
                                            <h6 class="font-extrabold mb-0">{{ number_format($totalPembelian) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Card Penjualan -->
                    <div class="col-6 col-lg-4 col-md-6">
                        <a href="{{ route('penjualan.index') }}" class="text-decoration-none text-dark">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon green">
                                                <i class="iconly-boldAdd-User"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Penjualan</h6>
                                            <h6 class="font-extrabold mb-0">{{ number_format($totalPenjualan) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
                <div class="row">
                    <div class="col-12 col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label for="tahun" class="form-label">Filter Tahun</label>
                                            <select name="tahun" id="tahun" class="form-control"
                                                onchange="this.form.submit()">
                                                @foreach ($tahunList as $tahun)
                                                    <option value="{{ $tahun }}"
                                                        {{ $tahun == $tahunDipilih ? 'selected' : '' }}>
                                                        {{ $tahun }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Grafik Pembelian & Penjualan Tahun {{ $tahunDipilih }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="chart-penjualan-pembelian"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Barang Restok</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" style="table-layout: fixed; width: 100%;" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center">Stok</th>
                                            <th class="text-center">ROP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($produks as $produk)
                                            <tr>
                                                <td>{{ $produk->kode }}</td>
                                                <td>{{ $produk->nama }}</td>
                                                <td class="text-center">{{ $produk->total_stok }}</td>
                                                <td class="text-center">{{ $produk->rop }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    Semua produk memiliki stok yang cukup
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Barang Kadaluwarsa</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" style="table-layout: fixed; width: 100%;" id="table2">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center">Kadaluwarsa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($batchs as $batch)
                                            <tr>
                                                <td>batch {{ $batch->id }}</td>
                                                <td>{{ $batch->produk->nama }}</td>
                                                <td class="text-center">{{ $batch->jumlah }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->translatedFormat('d F Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    Tidak ada barang yang akan kadaluwarsa dalam 2 bulan ke depan
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ✅ SweetAlert untuk notifikasi stok menipis setelah login --}}
    @if (session('restok_alert'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan Stok!',
                    html: '<div style="text-align: left;">' +
                        '<p><strong>{{ session('restok_alert') }}</strong></p>' +
                        '<small class="text-muted">Silakan periksa dan lakukan restok untuk produk tersebut.</small>' +
                        '</div>',
                    confirmButtonText: 'Lihat Produk',
                    confirmButtonColor: '#28a745',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: true,
                    cancelButtonText: 'Nanti Saja',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('produk.index') }}";
                    }
                });
            });
        </script>
    @endif

    {{-- ✅ Optional: Notifikasi sukses login --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ubah label bulan dari format 'YYYY-MM' jadi nama bulan (Januari, dst)
            const bulanLabels = @json($bulanLabels);
            const bulanNames = bulanLabels.map(label => {
                const bulan = new Date(label + '-01').toLocaleString('id-ID', {
                    month: 'long'
                });
                return bulan.charAt(0).toUpperCase() + bulan.slice(1); // Kapitalisasi huruf awal
            });

            var options = {
                chart: {
                    type: 'bar',
                    height: 400
                },
                plotOptions: {
                    bar: {
                        horizontal: false
                    }
                },
                series: [{
                        name: 'Pembelian',
                        data: @json(array_values($pembelianPerBulan->only($bulanLabels->all())->toArray()))
                    },
                    {
                        name: 'Penjualan',
                        data: @json(array_values($penjualanPerBulan->only($bulanLabels->all())->toArray()))
                    }
                ],
                xaxis: {
                    categories: bulanNames, // Nama bulan ditampilkan di bawah
                    title: {
                        text: 'Bulan'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Barang'
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -10
                },
                stroke: {
                    width: 1
                },
                colors: ['#1E90FF', '#28A745']
            };

            new ApexCharts(document.querySelector("#chart-penjualan-pembelian"), options).render();
        });
    </script>
@endsection

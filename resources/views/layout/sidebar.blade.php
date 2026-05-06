<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('dashboard') }}"><img src="{{ asset('images/logo.png') }}" style="width: 70px; height: auto;" alt="Logo" srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-columns"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if (auth()->user()->role === 'admin')
                @endif

                <li class="sidebar-item {{ request()->routeIs('produk.index') ? 'active' : '' }}">
                    <a href="{{ route('produk.index') }}" class='sidebar-link'>
                        <i class="bi bi-box-seam"></i>
                        <span>Produk</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('batch.index') ? 'active' : '' }}">
                    <a href="{{ route('batch.index') }}" class='sidebar-link'>
                        <i class="bi bi-box-seam"></i>
                        <span>Batch</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('pembelian.index') ? 'active' : '' }}">
                    <a href="{{ route('pembelian.index') }}" class='sidebar-link'>
                        <i class="bi bi-cart-check"></i>
                        <span>Pembelian</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('penjualan.index') ? 'active' : '' }}">
                    <a href="{{ route('penjualan.index') }}" class='sidebar-link'>
                        <i class="bi bi-clipboard-check"></i>
                        <span>Penjualan</span>
                    </a>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>

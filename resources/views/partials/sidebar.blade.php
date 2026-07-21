<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            Supply Chain<br>Management
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door-fill"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('countries.index') }}" class="nav-link-custom {{ request()->routeIs('countries.index') ? 'active' : '' }}">
        <i class="bi bi-globe2"></i>
        <span>Negara</span>
    </a>

    <a href="{{ route('risks.index') }}" class="nav-link-custom {{ request()->routeIs('risks.index') ? 'active' : '' }}">
        <i class="bi bi-shield-check"></i>
        <span>Risiko</span>
    </a>

    <a href="{{ route('ports.index') }}" class="nav-link-custom {{ request()->routeIs('ports.index') ? 'active' : '' }}">
        <i class="bi bi-pin-map-fill"></i>
        <span>Pelabuhan</span>
    </a>

    <a href="{{ route('currencies.index') }}" class="nav-link-custom {{ request()->routeIs('currencies.index') ? 'active' : '' }}">
        <i class="bi bi-currency-dollar"></i>
        <span>Kurs</span>
    </a>

    <a href="{{ route('news.index') }}" class="nav-link-custom {{ request()->routeIs('news.index') ? 'active' : '' }}">
        <i class="bi bi-newspaper"></i>
        <span>Berita</span>
    </a>

    <a href="{{ route('comparisons.index') }}" class="nav-link-custom {{ request()->routeIs('comparisons.index') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line"></i>
        <span>Perbandingan</span>
    </a>

    <a href="{{ route('watchlists.index') }}" class="nav-link-custom {{ request()->routeIs('watchlists.index') ? 'active' : '' }}">
        <i class="bi bi-star"></i>
        <span>Watchlist</span>
    </a>

    <a href="{{ route('reports.index') }}" class="nav-link-custom {{ request()->routeIs('reports.index') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Laporan</span>
    </a>

    @if (auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('admin.index') }}" class="nav-link-custom {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i>
            <span>Admin</span>
        </a>
    @endif

    @auth
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="flex-grow-1">
                <div style="font-weight: 700;">{{ auth()->user()->name }}</div>
                <div style="font-size: 13px; color: #bfdbfe;">{{ auth()->user()->email }}</div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light w-100">
                        <i class="bi bi-box-arrow-right"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    @endauth
</aside>

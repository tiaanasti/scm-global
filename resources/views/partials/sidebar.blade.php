<div class="sidebar-inner">
    {{-- BRAND --}}
    <a
        href="{{ route('dashboard') }}"
        class="brand"
        aria-label="Kembali ke Dashboard"
    >
        <span class="brand-icon" aria-hidden="true">
            <i class="bi bi-box-seam"></i>
        </span>

        <span class="brand-text">
            Supply Chain<br>
            Risk Intelligence
        </span>
    </a>

    {{-- MENU NAVIGASI --}}
    <nav class="sidebar-nav" aria-label="Navigasi utama">
        <a
            href="{{ route('dashboard') }}"
            class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            @if (request()->routeIs('dashboard')) aria-current="page" @endif
        >
            <i class="bi bi-house-door-fill" aria-hidden="true"></i>
            <span>Dashboard</span>
        </a>

        <a
            href="{{ route('countries.index') }}"
            class="nav-link-custom {{ request()->routeIs('countries.*') ? 'active' : '' }}"
            @if (request()->routeIs('countries.*')) aria-current="page" @endif
        >
            <i class="bi bi-globe2" aria-hidden="true"></i>
            <span>Negara</span>
        </a>

        <a
            href="{{ route('weather.index') }}"
            class="nav-link-custom {{ request()->routeIs('weather.*') ? 'active' : '' }}"
            @if (request()->routeIs('weather.*')) aria-current="page" @endif
        >
            <i class="bi bi-cloud-sun-fill" aria-hidden="true"></i>
            <span>Cuaca Global</span>
        </a>

        <a
            href="{{ route('risks.index') }}"
            class="nav-link-custom {{ request()->routeIs('risks.*') ? 'active' : '' }}"
            @if (request()->routeIs('risks.*')) aria-current="page" @endif
        >
            <i class="bi bi-shield-check" aria-hidden="true"></i>
            <span>Risiko</span>
        </a>

        <a
            href="{{ route('ports.index') }}"
            class="nav-link-custom {{ request()->routeIs('ports.*') ? 'active' : '' }}"
            @if (request()->routeIs('ports.*')) aria-current="page" @endif
        >
            <i class="bi bi-pin-map-fill" aria-hidden="true"></i>
            <span>Pelabuhan</span>
        </a>

        <a
            href="{{ route('currencies.index') }}"
            class="nav-link-custom {{ request()->routeIs('currencies.*') ? 'active' : '' }}"
            @if (request()->routeIs('currencies.*')) aria-current="page" @endif
        >
            <i class="bi bi-currency-dollar" aria-hidden="true"></i>
            <span>Kurs</span>
        </a>

        <a
            href="{{ route('news.index') }}"
            class="nav-link-custom {{ request()->routeIs('news.*') ? 'active' : '' }}"
            @if (request()->routeIs('news.*')) aria-current="page" @endif
        >
            <i class="bi bi-newspaper" aria-hidden="true"></i>
            <span>Berita</span>
        </a>

        <a
            href="{{ route('comparisons.index') }}"
            class="nav-link-custom {{ request()->routeIs('comparisons.*') ? 'active' : '' }}"
            @if (request()->routeIs('comparisons.*')) aria-current="page" @endif
        >
            <i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i>
            <span>Perbandingan</span>
        </a>

        <a
            href="{{ route('watchlists.index') }}"
            class="nav-link-custom {{ request()->routeIs('watchlists.*') ? 'active' : '' }}"
            @if (request()->routeIs('watchlists.*')) aria-current="page" @endif
        >
            <i class="bi bi-star-fill" aria-hidden="true"></i>
            <span>Watchlist</span>
        </a>

        <a
            href="{{ route('reports.index') }}"
            class="nav-link-custom {{ request()->routeIs('reports.*') ? 'active' : '' }}"
            @if (request()->routeIs('reports.*')) aria-current="page" @endif
        >
            <i
                class="bi bi-file-earmark-bar-graph-fill"
                aria-hidden="true"
            ></i>
            <span>Laporan</span>
        </a>

        @auth
            @if ((auth()->user()->role ?? 'user') === 'admin')
                <a
                    href="{{ route('admin.index') }}"
                    class="nav-link-custom {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                    @if (request()->routeIs('admin.*')) aria-current="page" @endif
                >
                    <i class="bi bi-person-gear" aria-hidden="true"></i>
                    <span>Admin</span>
                </a>
            @endif
        @endauth
    </nav>
</div>
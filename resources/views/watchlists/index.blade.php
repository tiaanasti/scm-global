<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pantauan - Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>

<body>
<div class="app">
    <!-- SIDEBAR -->
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

        <a href="{{ route('admin.index') }}" class="nav-link-custom {{ request()->routeIs('admin.index') ? 'active' : '' }}">
    <i class="bi bi-person-gear"></i>
    <span>Admin</span>
</a>

        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div style="font-weight: 700;">Admin</div>
                <div style="font-size: 13px; color: #bfdbfe;">Administrator</div>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">
        <div class="topbar">
            <div class="page-title">
                <h1>Daftar Pantauan</h1>
                <p>Negara yang dipantau untuk monitoring risiko rantai pasok secara berkelanjutan.</p>
            </div>
        </div>

        <div class="content">
            <!-- SUMMARY CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-star"></i>
                        </div>
                        <div>
                            <div class="metric-label">Total Watchlist</div>
                            <div class="metric-value">{{ $summary['total_watchlist'] }}</div>
                            <div class="metric-sub">Negara dipantau</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Risiko Tinggi</div>
                            <div class="metric-value">{{ $summary['high_risk'] }}</div>
                            <div class="metric-sub">Butuh perhatian utama</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-dash-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Risiko Sedang</div>
                            <div class="metric-value">{{ $summary['medium_risk'] }}</div>
                            <div class="metric-sub">Perlu dipantau</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Risiko Rendah</div>
                            <div class="metric-value">{{ $summary['low_risk'] }}</div>
                            <div class="metric-sub">Relatif aman</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WATCHLIST TABLE -->
            <div class="card-clean mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title mb-0">Negara dalam Daftar Pantauan</div>
                    <span class="risk-badge risk-medium">Monitoring aktif</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Wilayah</th>
                            <th>Kurs</th>
                            <th>Cuaca</th>
                            <th>Skor Risiko</th>
                            <th>Status</th>
                            <th>Mulai Dipantau</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($watchlistRows as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->country_name }}</strong>
                                    <div class="metric-sub">{{ $item->currency_code }}</div>
                                </td>
                                <td>{{ $item->region ?? '-' }}</td>
                                <td>
                                    USD/{{ $item->target_currency ?? '-' }}
                                    <div class="metric-sub">
                                        {{ number_format($item->exchange_rate ?? 0, 2) }}
                                    </div>
                                </td>
                                <td>
                                    {{ $item->temperature ?? 0 }}°C
                                    <div class="metric-sub">{{ $item->weather_status ?? '-' }}</div>
                                </td>
                                <td>
                                    <strong>{{ $item->total_score ?? 0 }}/100</strong>
                                </td>
                                <td>
                                    <span class="risk-badge
                                        {{ ($item->total_score ?? 0) >= 60 ? 'risk-high' : (($item->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                        {{ $item->risk_level ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">
                                    Belum ada negara dalam daftar pantauan.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RECOMMENDATION CARDS -->
            <div class="row g-4">
                @foreach ($watchlistRows as $item)
                    <div class="col-lg-6">
                        <div class="card-clean">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="section-title mb-1">{{ $item->country_name }}</div>
                                    <div class="metric-sub">{{ $item->region ?? '-' }}</div>
                                </div>

                                <span class="risk-badge
                                    {{ ($item->total_score ?? 0) >= 60 ? 'risk-high' : (($item->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                    {{ $item->risk_level ?? '-' }}
                                </span>
                            </div>

                            <div class="recommendation-box">
                                <strong>Catatan Pemantauan:</strong>
                                <div class="mt-2">
                                    {{ $item->recommendation ?? 'Belum ada rekomendasi untuk negara ini.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="footer">
                © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
            </div>
        </div>
    </main>
</div>
</body>
</html>
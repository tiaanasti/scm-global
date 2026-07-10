<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Supply Chain Management</title>
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
                <h1>Panel Admin</h1>
                <p>Kelola ringkasan data sistem, user, negara, pelabuhan, artikel, API log, dan kamus sentimen.</p>
            </div>
        </div>

        <div class="content">
            <!-- SUMMARY -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="metric-label">User</div>
                            <div class="metric-value">{{ $summary['users_count'] }}</div>
                            <div class="metric-sub">Pengguna sistem</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-globe2"></i>
                        </div>
                        <div>
                            <div class="metric-label">Negara</div>
                            <div class="metric-value">{{ $summary['countries_count'] }}</div>
                            <div class="metric-sub">Data negara</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-pin-map-fill"></i>
                        </div>
                        <div>
                            <div class="metric-label">Pelabuhan</div>
                            <div class="metric-value">{{ $summary['ports_count'] }}</div>
                            <div class="metric-sub">Dataset pelabuhan</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <div>
                            <div class="metric-label">Berita</div>
                            <div class="metric-value">{{ $summary['news_count'] }}</div>
                            <div class="metric-sub">News cache</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-purple">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div>
                            <div class="metric-label">Artikel</div>
                            <div class="metric-value">{{ $summary['articles_count'] }}</div>
                            <div class="metric-sub">Artikel analisis</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-star"></i>
                        </div>
                        <div>
                            <div class="metric-label">Watchlist</div>
                            <div class="metric-value">{{ $summary['watchlists_count'] }}</div>
                            <div class="metric-sub">Negara dipantau</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Positive Words</div>
                            <div class="metric-value">{{ $summary['positive_words_count'] }}</div>
                            <div class="metric-sub">Kamus positif</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-dash-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Negative Words</div>
                            <div class="metric-value">{{ $summary['negative_words_count'] }}</div>
                            <div class="metric-sub">Kamus negatif</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- USERS + API LOGS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Data User</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Dibuat</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">API Logs</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>API</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($apiLogs as $log)
                                    <tr>
                                        <td>
                                            <strong>{{ $log->api_name }}</strong>
                                            <div class="metric-sub">{{ $log->endpoint ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="risk-badge risk-low">{{ $log->status }}</span>
                                        </td>
                                        <td>
                                            {{ $log->requested_at ? \Carbon\Carbon::parse($log->requested_at)->format('d M Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted">Belum ada log API.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COUNTRIES TABLE -->
            <div class="card-clean mb-4">
                <div class="section-title">Dataset Negara dan Risiko</div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Wilayah</th>
                            <th>Mata Uang</th>
                            <th>Skor Risiko</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($countries as $country)
                            <tr>
                                <td><strong>{{ $country->name }}</strong></td>
                                <td>{{ $country->region ?? '-' }}</td>
                                <td>{{ $country->currency_code ?? '-' }}</td>
                                <td>{{ $country->total_score ?? 0 }}/100</td>
                                <td>
                                    <span class="risk-badge
                                        {{ ($country->total_score ?? 0) >= 60 ? 'risk-high' : (($country->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                        {{ $country->risk_level ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PORTS + ARTICLES -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Dataset Pelabuhan</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Pelabuhan</th>
                                    <th>Negara</th>
                                    <th>Status</th>
                                    <th>Risiko</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($ports as $port)
                                    <tr>
                                        <td>
                                            <i class="bi bi-pin-map-fill text-primary"></i>
                                            <strong>{{ $port->name }}</strong>
                                            <div class="metric-sub">{{ $port->city ?? '-' }}</div>
                                        </td>
                                        <td>{{ $port->country_name ?? '-' }}</td>
                                        <td>
                                            <span class="risk-badge
                                                {{ $port->status === 'Aman' ? 'risk-low' : ($port->status === 'Waspada' ? 'risk-high' : 'risk-medium') }}">
                                                {{ $port->status }}
                                            </span>
                                        </td>
                                        <td>{{ $port->port_risk_score }}/100</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Artikel Analisis</div>

                        @forelse ($articles as $article)
                            <div class="news-item">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="risk-badge {{ $article->status === 'Published' ? 'risk-low' : 'risk-medium' }}">
                                        {{ $article->status }}
                                    </span>
                                    <small class="text-muted">{{ $article->category ?? '-' }}</small>
                                </div>

                                <div class="news-title">{{ $article->title }}</div>
                                <div class="news-desc">
                                    Penulis: {{ $article->author_name ?? 'Admin' }}
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada artikel.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- SENTIMENT WORDS -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Kamus Kata Positif</div>

                        @foreach ($positiveWords as $word)
                            <span class="risk-badge risk-low me-1 mb-2">
                                {{ $word->word }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Kamus Kata Negatif</div>

                        @foreach ($negativeWords as $word)
                            <span class="risk-badge risk-high me-1 mb-2">
                                {{ $word->word }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
            </div>
        </div>
    </main>
</div>
</body>
</html>
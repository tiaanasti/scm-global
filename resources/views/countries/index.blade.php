<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Intelijen Negara - Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
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

        <a href="{{ route('dashboard') }}" class="nav-link-custom">
    <i class="bi bi-house-door-fill"></i>
    Dashboard
</a>

<a href="{{ route('countries.index') }}" class="nav-link-custom active">
    <i class="bi bi-globe2"></i>
    Negara
</a>

<a href="{{ route('risks.index') }}" class="nav-link-custom">
    <i class="bi bi-shield-check"></i>
    Risiko
</a>

<a href="{{ route('ports.index') }}" class="nav-link-custom">
    <i class="bi bi-pin-map-fill"></i>
    Pelabuhan
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
                <h1>Intelijen Negara</h1>
                <p>Profil negara untuk memantau ekonomi, cuaca, kurs, pelabuhan, dan risiko rantai pasok.</p>
            </div>

            <form action="{{ route('countries.index') }}" method="GET">
                <select name="country_id" class="form-select country-select" onchange="this.form.submit()">
                    @foreach ($countries as $item)
                        <option value="{{ $item->id }}" {{ $selectedCountryId == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="content">
            <!-- TOP CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-flag"></i>
                        </div>
                        <div>
                            <div class="metric-label">Negara Dipilih</div>
                            <div class="metric-value">{{ $selectedCountry->name ?? '-' }}</div>
                            <div class="metric-sub">{{ $selectedCountry->region ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <div class="metric-label">GDP</div>
                            <div class="metric-value">
                                ${{ number_format(($economic->gdp ?? 0) / 1000000000000, 2) }}T
                            </div>
                            <div class="metric-sub">Produk domestik bruto</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div>
                            <div class="metric-label">Inflasi</div>
                            <div class="metric-value">{{ $economic->inflation_rate ?? 0 }}%</div>
                            <div class="metric-sub">Tekanan harga tahunan</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <div>
                            <div class="metric-label">Skor Risiko</div>
                            <div class="metric-value">{{ $risk->total_score ?? 0 }}/100</div>
                            <div class="metric-sub">
                                <span class="risk-badge
                                    {{ ($risk->total_score ?? 0) >= 60 ? 'risk-high' : (($risk->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                    {{ $risk->risk_level ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROFILE + INDICATORS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Profil Negara</div>

                        <div class="info-row">
                            <div class="info-label">Ibu Kota</div>
                            <div class="info-value">{{ $selectedCountry->capital ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Wilayah</div>
                            <div class="info-value">{{ $selectedCountry->region ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Kode Negara</div>
                            <div class="info-value">{{ $selectedCountry->country_code ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Mata Uang</div>
                            <div class="info-value">{{ $selectedCountry->currency_code ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Nama Mata Uang</div>
                            <div class="info-value">{{ $selectedCountry->currency_name ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Bahasa</div>
                            <div class="info-value">{{ $selectedCountry->language ?? '-' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Populasi</div>
                            <div class="info-value">{{ number_format($economic->population ?? 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Indikator Rantai Pasok</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="indicator-box indicator-blue">
                                    <div class="metric-label">Cuaca Saat Ini</div>
                                    <div class="metric-value">{{ $weather->temperature ?? 0 }}°C</div>
                                    <div class="metric-sub">
                                        {{ $weather->weather_status ?? '-' }},
                                        angin {{ $weather->wind_speed ?? 0 }} km/jam
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="indicator-box indicator-purple">
                                    <div class="metric-label">Kurs USD/{{ $currency->target_currency ?? '-' }}</div>
                                    <div class="metric-value">
                                        {{ number_format($currency->exchange_rate ?? 0, 2) }}
                                    </div>
                                    <div class="metric-sub">
                                        Perubahan {{ $currency->change_percentage ?? 0 }}%
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="indicator-box indicator-green">
                                    <div class="metric-label">Ekspor</div>
                                    <div class="metric-value">
                                        ${{ number_format(($economic->exports ?? 0) / 1000000000, 2) }}B
                                    </div>
                                    <div class="metric-sub">Nilai ekspor tahunan</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="indicator-box indicator-orange">
                                    <div class="metric-label">Impor</div>
                                    <div class="metric-value">
                                        ${{ number_format(($economic->imports ?? 0) / 1000000000, 2) }}B
                                    </div>
                                    <div class="metric-sub">Nilai impor tahunan</div>
                                </div>
                            </div>
                        </div>

                        <div class="recommendation-box mt-4">
                            <strong>Rekomendasi Sistem:</strong>
                            <div class="mt-2">
                                {{ $risk->recommendation ?? 'Belum ada rekomendasi untuk negara ini.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PORTS + NEWS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Pelabuhan Terkait</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Pelabuhan</th>
                                    <th>Kota</th>
                                    <th>Status</th>
                                    <th>Risiko</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($ports as $port)
                                    <tr>
                                        <td>
                                            <i class="bi bi-anchor text-primary"></i>
                                            <strong>{{ $port->name }}</strong>
                                        </td>
                                        <td>{{ $port->city ?? '-' }}</td>
                                        <td>
                                            <span class="risk-badge
                                                {{ $port->status === 'Aman' ? 'risk-low' : ($port->status === 'Waspada' ? 'risk-high' : 'risk-medium') }}">
                                                {{ $port->status }}
                                            </span>
                                        </td>
                                        <td>{{ $port->port_risk_score }}/100</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">Belum ada data pelabuhan.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Berita Negara</div>

                        @forelse ($news as $item)
                            <div class="news-item">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="risk-badge
                                        {{ $item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium') }}">
                                        {{ $item->sentiment }}
                                    </span>
                                    <small class="text-muted">{{ $item->category ?? 'Umum' }}</small>
                                </div>
                                <div class="news-title">{{ $item->title }}</div>
                                <div class="news-desc">{{ $item->description }}</div>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada berita untuk negara ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- ALL COUNTRIES TABLE -->
            <div class="card-clean">
                <div class="section-title">Daftar Semua Negara</div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Wilayah</th>
                            <th>Mata Uang</th>
                            <th>GDP</th>
                            <th>Inflasi</th>
                            <th>Populasi</th>
                            <th>Skor Risiko</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($countryRows as $row)
                            <tr>
                                <td><strong>{{ $row->name }}</strong></td>
                                <td>{{ $row->region ?? '-' }}</td>
                                <td>{{ $row->currency_code ?? '-' }}</td>
                                <td>${{ number_format(($row->gdp ?? 0) / 1000000000000, 2) }}T</td>
                                <td>{{ $row->inflation_rate ?? 0 }}%</td>
                                <td>{{ number_format($row->population ?? 0) }}</td>
                                <td>{{ $row->total_score ?? 0 }}/100</td>
                                <td>
                                    <span class="risk-badge
                                        {{ ($row->total_score ?? 0) >= 60 ? 'risk-high' : (($row->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                        {{ $row->risk_level ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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